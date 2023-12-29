<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        $produits = $productsRepository->findAll();
        //return $this->render('', compact('produits'));
        return $this->render('admin/products/index.html.twig',[
            'produits' => $produits,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request,PictureService $pic, EntityManagerInterface $em, SluggerInterface $slugger,CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un "nouveau produit"
        $product = new Products();

        // On crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        // On traite la requête du formulaire
        $productForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($productForm->isSubmitted() && $productForm->isValid()){
            // On récupère les images            
           $images=$productForm->get('images')->getData();
            foreach($images as $image){
                $folder ='products';
                $fichier=$pic->add($image,$folder,300,300);
                $img= new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }
            // On génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            // On arrondit le prix 
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

            // On stocke
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            // On redirige
            return $this->redirectToRoute('admin_products_index');
        }


        // return $this->render('admin/products/add.html.twig',[
        //     'productForm' => $productForm->createView()
        // ]);

        //return $this->renderForm('', compact('productForm'));
        return $this->renderForm('admin/products/add.html.twig',[
            'productForm' => $productForm,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
        // ['productForm' => $productForm]
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Products $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger,CategoriesRepository $categoriesRepository , ProductsRepository $productsRepository): Response
    {
        // On vérifie si l'utilisateur peut éditer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        // On divise le prix par 100
        // $prix = $product->getPrice() / 100;
        // $product->setPrice($prix);

        // On crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        // On traite la requête du formulaire
        $productForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($productForm->isSubmitted() && $productForm->isValid()){
            // On récupère les images

            
            // On génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            // On arrondit le prix 
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

            // On stocke
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès');

            // On redirige
            return $this->redirectToRoute('admin_products_index');
        }


        return $this->render('admin/products/edit.html.twig',[
            'productForm' => $productForm->createView(),
            'product' => $product,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);

        // return $this->renderForm('admin/products/edit.html.twig', compact('productForm'));
        // ['productForm' => $productForm]
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product,EntityManagerInterface $entityManager): Response
    {
        // On vérifie si l'utilisateur peut supprimer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        $entityManager->remove($product);

        $entityManager->flush();
            return $this->redirectToRoute('admin_products_index');
    }

    

}