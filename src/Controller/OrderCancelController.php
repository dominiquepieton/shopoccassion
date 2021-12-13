<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    private $entityManager;
    private $orderRepository;
    
    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }

    
    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {

        $order = $this->orderRepository->findOneByStripeSessionId($stripeSessionId);

        // redirection en cas d'erreur
        if(!$order || $order->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('home');
        }    

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
