<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/reset-password", name= "reset_password")
     *
     */
    public function index(Request $request): Response
    {
        // Si user connecté on le redirige vers la homepage
        if($this->getUser())
        {
            return $this->redirectToRoute('home');
        }
        // Vérification si email est bien rentré
        if($request->get('email'))
        {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if($user)
            {
                // enregistrement dans la bdd
                $date = new \DateTimeImmutable();
                $reset_pwd = new ResetPassword();
                $reset_pwd->setUser($user);
                $reset_pwd->setToken(uniqid());
                $reset_pwd->setCreatedAt($date);
                $this->entityManager->persist($reset_pwd);
                $this->entityManager->flush();

                // envois d'email à l'user avec un lien pour réinitialiser son pwd
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_pwd->getToken()
                ]);
                
                $content = "bonjour ".$user->getFirstname()."<br/>Vous avez demandé à réinitialiser votre password sur ShopOccassion.<br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant afin de <a href='".$url."'>modifier votre password.</a>";
                
                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre password sur le site ShopOccassion.', $content);
            
                $this->addFlash('notice', 'Allez vérifier votre boite mail, un email vous attends afin de modifier le password.');
            } else {
                $this->addFlash('notice', "l'email renseigné est inconnu....");
            }
        }

        return $this->render('reset_password/index.html.twig');
    }


    /**
     * @Route("/reset-password/{token}", name="update_password")
     *
     */
    public function update($token, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        
        $reset_pwd = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_pwd)
        {
            return $this->redirectToRoute('reset_password');
        }

        // verifier si createdAt = now + 3h
        $now = new \DateTimeImmutable();
        if($now > $reset_pwd->getCreatedAt()->modify('+ 3 hour'))
        {
            // si le token à expirer
            $this->addFlash('notice', 'Votre demande de modification de password a expirée.');
            return $this->redirectToRoute('reset_password');
        }
        
        // rendre la vu de la modification password
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
           
            $new_pwd = $form->get('new_password')->getData();
            // Encodage du password
            $password = $encoder->encodePassword($reset_pwd->getUser(), $new_pwd);
            $reset_pwd->getUser()->setPassword($password);
            // flush à la bdd
            $this->entityManager->flush();
            // redirection vers la page de connexion
            $this->addFlash('notice', 'Votre password a bien été mis à jour.');
            
            return $this->redirectToRoute('app_login');

        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);  
        
    }
}
