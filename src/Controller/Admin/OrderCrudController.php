<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;

    private $crudUrlGenerator; // pour generer l'url de redirection
    
    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }
    
    
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Ajout d'action cuustom (name de l'action, le label, icone pour l'interface)
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');
        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    /**
     * change le statis de la commande
     */
    public function updatePreparation(AdminContext $context)
    {
        // récuperation de l'order choisi
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice' , "
            <span><strong>La commande : ".$order->getReference()." est bien en cours de préparation.</strong></span>
        ");


        $email = new Mail();
        $content = "Bonjour ".$order->getUser()->getFullName()."<br/>
                    Bienvenue sur la boutique qui permet de donner une seconde vie à divers objet.<br/>
                    Votre commande n° : ".$order->getReference()." est en cours de préparation.";

        $email->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Votre commande n°: ".$order->getReference(), $content );
        // Redirection on utilise crudurlgenerator (on lui passe le controller et action )
        //$url = $this->crudUrlGenerator->build()
           // ->setController(OrderCrudController::class)
           // ->setAction('index')
           // ->generateUrl();
           //return $this->redirect($url);
           
           $routeBuilder = $this->get(AdminUrlGenerator::class);
           return $this->redirect($routeBuilder->setController(OrderCrudController::class)->setAction('index')->generateUrl());  
    }

    public function updateDelivery(AdminContext $context)
    {
        // récuperation de l'order choisi
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice' , "
            <span><strong>La commande : ".$order->getReference()." est bien en cours de livraison.</strong></span>
        ");

        $email = new Mail();
        $content = "Bonjour ".$order->getUser()->getFullName()."<br/>
                    Bienvenue sur la boutique qui permet de donner une seconde vie à divers objet.<br/>
                    Votre commande n° : ".$order->getReference()." est en cours de livraison.";

        $email->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Votre commande n°: ".$order->getReference(), $content );
        // Redirection on utilise crudurlgenerator (on lui passe le controller et action )
        //$url = $this->crudUrlGenerator->build()
           // ->setController(OrderCrudController::class)
           // ->setAction('index')
           // ->generateUrl();
           //return $this->redirect($url);
           
           $routeBuilder = $this->get(AdminUrlGenerator::class);
           return $this->redirect($routeBuilder->setController(OrderCrudController::class)->setAction('index')->generateUrl());  
    }



    public function configureCrud(Crud $crud): Crud
    {
       return $crud->setDefaultSort(['id'=>'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt', 'Passée le'),
            TextField::new('user.getFullName', 'Utilisateur'),
            TextField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total')->setCurrency('EUR'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state', 'status')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }  
}
