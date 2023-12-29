<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;

#[Route('/produits', name: 'products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository): Response
    {

        return $this->render('products/index.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository,Products $product): Response
    {
       // return $this->render('', compact('product'));
        return $this->render('products/details.html.twig', [
            'product' => $product,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc'])
        ]);
    }
}