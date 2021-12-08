<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    private $entityManager;
    private $addressRepository;

    
    public function __construct(EntityManagerInterface $entityManager, AddressRepository $addressRepository)
    {
        $this->entityManager = $entityManager;
        $this->addressRepository = $addressRepository;
    }
    
    /**
     * @Route("/compte/address", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }


    /**
     * Permet l'ajout d'une adresse pour l'utilisateur.
     * 
     * @Route("/compte/ajouter-une-adresse", name="account_address_add")
     */
    public function add(Request $request, Cart $cart): Response
    {
        
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $address->setUser($this->getUser());
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            if($cart->get())
            {
                return $this->redirectToRoute('order');
            }else{
                return $this->redirectToRoute('account_address');
            }

        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet l'ajout d'une adresse pour l'utilisateur.
     * 
     * @Route("/compte/modifier-une-adresse/{id}", name="account_address_edit")
     */
    public function edit(Request $request, $id): Response
    {
       
        $address = $this->addressRepository->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->flush();

            return $this->redirectToRoute('account_address');

        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet la suppression d'une adresse pour l'utilisateur.
     * 
     * @Route("/compte/supprimer-une-adresse/{id}", name="account_address_delete")
     */
    public function delete($id): Response
    {  
        $address = $this->addressRepository->findOneById($id);

        if($address || $address->getUser() == $this->getUser()){
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }
        
        return $this->redirectToRoute('account_address');

    }
}
