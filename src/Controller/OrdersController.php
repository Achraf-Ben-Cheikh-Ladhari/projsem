<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Repository\CategoriesRepository;
use App\Repository\OrdersRepository;
use App\Entity\Products;
use Doctrine\ORM\Mapping\Id;

#[Route('/commandes', name: 'app_orders_')]
class OrdersController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function add(SessionInterface $session, ProductsRepository $productsRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        if($panier === []){
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('main');
        }

        //Le panier n'est pas vide, on crée la commande
        $order = new Orders();

        // On remplit la commande
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());

        // On parcourt le panier pour créer les détails de commande
        foreach($panier as $item => $quantity){
            $orderDetails = new OrdersDetails();

            // On va chercher le produit
            $product = $productsRepository->find($item);
            
            $price = $product->getPrice();

            // On crée le détail de commande
            $orderDetails->setProducts($product);
            $orderDetails->setPrice($price);
            $orderDetails->setQuantity($quantity);

            $order->addOrdersDetail($orderDetails);
        }

        // On persiste et on flush
        $em->persist($order);
        $em->flush();

        $session->remove('panier');

        $this->addFlash('message', 'Commande créée avec succès');
        return $this->redirectToRoute('app_orders_show_details', ['id' => $order->getId()]);
    }


    #[Route('/show', name: 'show')]
    public function show( OrdersRepository $ordersRepository, ProductsRepository $productsRepository, UsersRepository $usersRepository,CategoriesRepository $categoriesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $users=new Users();
        //recherche user
        $currentLoggedInUser = $this->getUser();
        $users->setEmail($currentLoggedInUser->getUserIdentifier());
        //set values to current user
        $users = $usersRepository->findOneBy(['email' => $users->getEmail()]);

        return $this->render('profile/show.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc']), 
            'orders' => $ordersRepository->findBy(['users'=>$users], ['id' => 'asc'])
        ]);    
    }

    #[Route('/show/details/{id}', name: 'show_details')]
    public function details( Orders $order, OrdersRepository $ordersRepository,ProductsRepository $productsRepository, EntityManagerInterface $em,CategoriesRepository $categoriesRepository,UsersRepository $usersRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $users=new Users();
        //recherche user
        $currentLoggedInUser = $this->getUser();
        $users->setEmail($currentLoggedInUser->getUserIdentifier());
        //set values to current user
        $users = $usersRepository->findOneBy(['email' => $users->getEmail()]);
        
        $orderD = $em->getRepository(OrdersDetails::class)->findBy(['orders' => $order]);
        // On remplit la commande
       //$product=new Products();
       

        $product= $em->getRepository(Products::class)->findBy(['id' => $orderD]);
        
        return $this->render('profile/orderDetails.html.twig', [
            'products'=>$product,
            'ordersD' => $orderD,
            'categories' => $categoriesRepository->findBy([], ['name' => 'asc']), 'prod' => $productsRepository->findBy([], ['name' => 'asc']), 
            'orders' => $ordersRepository->findBy(['users'=>$users], ['id' => 'asc'])
        ]);    



    }

    

}
