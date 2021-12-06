<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/compte/update-password", name="account_password")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        // système d'information pour l'utilisateur
        $notification = null;
        $notificationError = null;

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // Récupération de l'ancien password dans le formulaire
            $old_password = $form->get('old_password')->getData();
            
            // comparaison du password saisi avec password bdd
            if($encoder->isPasswordValid($user, $old_password))
            {
                $new_pwd = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($user, $new_pwd);

                $user->setPassword($password);
                $this->entityManager->flush();
                $notification = "Votre password a bien été mis à jour.";
            }else{
                $notificationError = "Votre password actuel n'est pas valide.";
            }
        }
        
        return $this->render('account/password.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification,
            'notificationError' => $notificationError
        ]);
    }
}
