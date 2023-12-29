<?php

namespace App\Controller\Admin;

use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use App\Form\CategoriesFormType;

#[Route('/admin/categories', name: 'admin_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        $categoriess = $categoriesRepository->findBy([], ['id' => 'asc']);

        //return $this->render('admin/categories/index.html.twig', compact('categories'));
        return $this->render('admin/categories/index.html.twig', [
            'category' => $categoriess,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger,CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un "nouveau produit"
        $categorie = new Categories();

        // On crée le formulaire
        $categorieForm = $this->createForm(CategoriesFormType::class, $categorie);

        // On traite la requête du formulaire
        $categorieForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($categorieForm->isSubmitted() && $categorieForm->isValid()){
            // On récupère les images            
           

            // On génère le slug
            $slug = $slugger->slug($categorie->getName())->lower();
            $categorie->setSlug($slug);


            // On stocke
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Categorie ajouté avec succès');

            // On redirige
            return $this->redirectToRoute('admin_categories_index');

        }


        // return $this->render('admin/categories/add.html.twig',[
        //     'categorieForm' => $categorieForm->createView()
        // ]);

       // return $this->renderForm('', compact('categorieForm'));
        // ['categorieForm' => $categorieForm]
        return $this->renderForm('admin/categories/add.html.twig', [
            'categorieForm' => $categorieForm,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }


    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Categories $categorie, Request $request, EntityManagerInterface $em, SluggerInterface $slugger,CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        // On vérifie si l'utilisateur peut éditer avec le Voter
        $this->denyAccessUnlessGranted('ROLE_ADMIN', $categorie);

        // On divise le prix par 100
        // $prix = $categorie->getPrice() / 100;
        // $categorie->setPrice($prix);

        // On crée le formulaire
        $categorieForm = $this->createForm(CategoriesFormType::class, $categorie);

        // On traite la requête du formulaire
        $categorieForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($categorieForm->isSubmitted() && $categorieForm->isValid()){
            // On récupère les images

            
            
            // On génère le slug
            $slug = $slugger->slug($categorie->getName())->lower();
            $categorie->setSlug($slug);

            // On arrondit le prix 
            // $prix = $categorie->getPrice() * 100;
            // $categorie->setPrice($prix);

            // On stocke
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Categorie modifié avec succès');

            // On redirige
            return $this->redirectToRoute('admin_categories_index');
        }


        return $this->render('admin/categories/edit.html.twig',[
            'categorieForm' => $categorieForm->createView(),
            'categorie' => $categorie,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);

        // return $this->renderForm('admin/Categories/edit.html.twig', compact('categorieForm'));
        // ['categorieForm' => $categorieForm]
    }

}