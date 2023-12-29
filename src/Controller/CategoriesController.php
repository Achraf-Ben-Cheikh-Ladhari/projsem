<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Images;
use App\Repository\CategoriesRepository;
use App\Repository\ImagesRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    

    #[Route('/{slug}', name: 'list')]
    public function list(Categories $category,ImagesRepository $imagesRepository,CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository, Request $request): Response
    {
        //On va chercher le numéro de page dans l'url
        $page = $request->query->getInt('page', 1);
        //On va chercher la liste des produits de la catégorie
        $products = $productsRepository->findProductsPaginated($page, $category->getSlug(), 4);

        //return $this->render('categories/list.html.twig', compact('category', 'prod'));
        // Syntaxe alternative
         return $this->render('categories/list.html.twig', [
             'images'=> $imagesRepository->findBy([], ['name' => 'asc']),
             'category' => $category,
             'products' => $products,
             'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
         ]);
    }
}