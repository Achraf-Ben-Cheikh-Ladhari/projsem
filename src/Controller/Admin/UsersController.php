<?php

namespace App\Controller\Admin;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use App\Form\UsersFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;

#[Route('/admin/utilisateurs', name: 'admin_users_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository,CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        $users = $usersRepository->findBy([], ['roles' => 'asc']);
       // return $this->render('', compact('users'));
        return $this->render('admin/users/index.html.twig',[
            'users' => $users,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Users $users, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher,CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        // On vérifie si l'utilisateur peut éditer avec le Voter
        $this->denyAccessUnlessGranted('ROLE_ADMIN', $users);

        // On crée le formulaire
        $usersForm = $this->createForm(UsersFormType::class, $users);

        // On traite la requête du formulaire
        $usersForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($usersForm->isSubmitted() && $usersForm->isValid()){
            $users->setRoles(['ROLE_PRODUCT_ADMIN']);

            // if password not null it will edit the password else not
            if ($usersForm->get('password')->getData()!=''){
            $users->setPassword(
                $userPasswordHasher->hashPassword(
                        $users,
                        $usersForm->get('password')->getData()
                    )
                );
            }

            $em->persist($users);
            $em->flush();
            $this->addFlash('success', 'User modifié avec succès');
            // On redirige
            return $this->redirectToRoute('admin_users_index');
        }


        return $this->render('admin/users/edit.html.twig',[
            'usersForm' => $usersForm->createView(),
            'users' => $users,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }



    #[Route('/ajout', name: 'add')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_PRODUCT_ADMIN']);
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User ajouter avec succès');
            // On redirige
            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/users/add.html.twig', [
            'usersForm' => $form->createView(),
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Users $user,EntityManagerInterface $entityManager): Response
    {
        // On vérifie si l'utilisateur peut supprimer avec le Voter
        $this->denyAccessUnlessGranted('ROLE_ADMIN', $user);
        $entityManager->remove($user);

        $entityManager->flush();
            return $this->redirectToRoute('admin_users_index');
    }
}