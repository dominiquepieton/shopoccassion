<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    
    private $entityManager;
    private $orderRepository;

    
    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }
     
    /**
     * @Route("/compte/mes-commandes", name="account_order")
     */
    public function index(): Response
    {
        $orders = $this->orderRepository->findSuccessOrder($this->getUser());
        //dd($orders);
        
        return $this->render('account/order.html.twig', ['orders' => $orders]);
    }


    /**
     * @Route("/compte/mes-commandes/{reference}", name="account_order_show")
     */
    public function show($reference): Response
    {
        $order = $this->orderRepository->findOneByReference($reference);
        if(!$order || $order->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('account_order');
        }
        
        return $this->render('account/orderShow.html.twig', ['order' => $order]);
    }
}
