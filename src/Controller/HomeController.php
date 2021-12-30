<?php

namespace App\Controller;

use App\Entity\Header;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Gestion de la page d'accueil :
 * 
 * 
 */
class HomeController extends AbstractController
{
    
    private $entityManager;
    private $addressRepository;

    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
        $headers = $this->entityManager->getRepository(Header::class)->findAll();
        
        return $this->render('home/homepage.html.twig', [
            'products' => $products,
            'headers' => $headers
        ]);
    }
}
