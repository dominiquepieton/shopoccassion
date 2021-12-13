<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
       // Gestion de stripe
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000'; // Remplacer par www.nomdu site.com

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        
        if(!$order)
        {
            new JsonResponse(['error' => 'order']);
        }

        

        foreach($order->getOrderDetails()->getValues() as $obj)
        {
            $productObjet = $entityManager->getRepository(Product::class)->findOneByName($obj->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $obj->getPrice(),
                    'product_data' => [
                        'name' => $obj->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$productObjet->getIllustration()],
                    ],
                ],
                'quantity' => $obj->getQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice() * 100,
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('');
        $checkout_session = Session::create([
           'customer_email' => $this->getUser()->getEmail(),
           'payment_method_types' => ['card'],
           'line_items' => [
               $products_for_stripe
           ],
           'mode' => 'payment',
           'success_url' => $YOUR_DOMAIN . '/success.html',
           'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
