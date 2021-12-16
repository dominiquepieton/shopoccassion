<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
    private $orderRepository;
    
    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }
    
    
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->orderRepository->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid())
        {
            // vide le panier
            $cart->remove();
            // on change la valeur de isPaid
            $order->setIsPaid(1);
            $this->entityManager->flush();
            // on envoit un mail de confirmation
            // Partie envoie de mail avec mailJet
            $email = new Mail();
            $content = "Bonjour ".$order->getUser()->getFullName()."<br/>
                        Bienvenue sur la boutique qui permet de donner une seconde vie à divers objet.<br/>
                        Votre commande est validée par le centre de paiement. La préparation sera trés dans la journée.";

            $email->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Votre commande chez ShopOccassion est bien validée", $content );

        }

        
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
