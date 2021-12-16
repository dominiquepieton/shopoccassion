<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/inscription", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            // Vérification si l'utilisateur n'est pas en base de donnée
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if(!$search_email)
            {
                $password = $encoder->encodePassword($user,$user->getPassword());
                $user->setPassword( $password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = "Votre inscription s'est déroulée avec succés. Vous pouvez vous connecter dés à present.";
            } else {
                $notification = "L'email est déjà utilisé.";
            }

            // Partie envoie de mail avec mailJet
            $email = new Mail();
            $content = "Bonjour ".$user->getFullName()."<br/>
                        Bienvenue sur la boutique qui permet de donner une seconde vie à divers objet.<br/>
                        Votre identifiant est : <strong>".$user->getEmail()."</strong>.";

            $email->send($user->getEmail(), $user->getFirstname(), "Bienvenue sur ShopOccassion", $content );
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
