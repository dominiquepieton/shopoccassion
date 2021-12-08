<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $entityManager;
    private $productRepository;
    
    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }
    
    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    {
        // vérification si l'user a enregistrer des adresses
        if(!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }
        
        $form = $this->createForm(OrderType::class, null, ['user' => $this->getUser()]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getAll()
        ]);
    }


    /**
     * @Route("/commande/recapitulatif", name="order_recap")
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $date = new \DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();

            // enregistrement de la commande order()
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());

            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br/>'.$delivery->getPhone();
            if($delivery->getCompany())
            {
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }
            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getPays();

            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);
            $this->entityManager->persist($order);

            // enregistrement des produits orderDetails()
            foreach($cart->getAll() as $obj)
            {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($obj['product']->getName());
                $orderDetails->setQuantity($obj['quantity']);
                $orderDetails->setPrice($obj['product']->getPrice());
                $orderDetails->setTotal($obj['product']->getPrice() * $obj['quantity']);

                $this->entityManager->persist($orderDetails);
            }

            //$this->entityManager->flush();


        }

        return $this->render('order/add.html.twig', [
            'cart' => $cart->getAll(),
            'carrier' => $carriers
        ]);
    }
}
