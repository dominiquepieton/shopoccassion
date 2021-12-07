<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/compte/address", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }


    /**
     * @Route("/compte/ajouter-une-adresse", name="account_address_add")
     */
    public function add(): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        return $this->render('account/address_add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
