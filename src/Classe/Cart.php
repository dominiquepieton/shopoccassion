<?php

namespace App\Classe;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;
    private $productRepository;
    
    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->session = $session;
    }

    /**
     * Permet l'ajout d'un article dans la session
     *
     */
    public function add($id)
    {
        // stockage de la session
        $cart = $this->session->get('cart', []);

        // si dans cart il y a déjà un produit
        if(!empty($cart[$id]))
        {
            // on increment la quantite pour l'id donnée
            $cart[$id]++;
        } else {
            // on donne 1 à l'id choisit
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }


    public function get()
    {
        return $this->session->get('cart');
    }


    public function remove()
    {
        return $this->session->remove('cart');
    }


    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }


     /**
     * Supprime un article dans le panier
     *
     */
    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);

        if($cart[$id] > 1)
        {
            //on decrement de 1
            $cart[$id]--;
            
        }else{
            // on supprime le produit
            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart);
    }


    public function getAll()
    {
        $cartComplete = [];

        if($this->get())
        {
            foreach($this->get() as $id => $quantity)
            {
                $product_object = $this->productRepository->findOneById($id);

                if(!$product_object){
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' =>  $product_object,
                    'quantity' => $quantity 
                ];
            }
        }

        return $cartComplete;
    }
}