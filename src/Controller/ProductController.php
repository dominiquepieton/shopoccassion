<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $entityManager;
    private $productRepository;
    
    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }
    
    
    
    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request): Response
    {
        $products = $this->productRepository->findAll();
        
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form)
        {
            $products = $this->productRepository->findWithSearch($search);
        }
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView() 
        ]);
    }


    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function show($slug): Response
    {
        $product = $this->productRepository->findOneBySlug($slug);
        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
        
        if(!$product)
        {
            return $this->redirectToRoute('products');
        }
        
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'products' => $products 
        ]);
    }
}
