<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UsersFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
#[Route('/profil', name: 'profile_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $em, Request $request,CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository,UserPasswordHasherInterface $userPasswordHasher,UsersRepository $usersRepository): Response
    {
        return $this->render('profile/index.html.twig',[
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);

    }
    
    #[Route('/edit', name: 'edit')]
    public function edit(EntityManagerInterface $em, Request $request,CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository,UserPasswordHasherInterface $userPasswordHasher,UsersRepository $usersRepository): Response
    {

        //+user
        $users=new Users();
        //recherche user
        $currentLoggedInUser = $this->getUser();
        $users->setEmail($currentLoggedInUser->getUserIdentifier());

        //set values to current user
        $users = $usersRepository->findOneBy(['email' => $users->getEmail()]);

        $usersForm = $this->createForm(UsersFormType::class, $users);
        // Set other properties accordingly
        // On traite la requête du formulaire
        $usersForm->handleRequest($request);
        //On vérifie si le formulaire est soumis ET valide
        if($usersForm->isSubmitted() && $usersForm->isValid()){
            //if password not null it will edit the password else not
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
            $this->addFlash('success', 'Compte modifié avec succès');
            // On redirige
            return $this->redirectToRoute('profile_index');
        }


        return $this->render('profile/edit.html.twig',[
            'usersForm' => $usersForm->createView(),
            'users' => $users,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
      
    }

    #[Route('/commandes', name: 'orders')]
    public function orders(CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Commandes de l\'utilisateur',
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }
}
