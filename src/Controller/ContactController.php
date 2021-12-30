<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->addFlash('notice', 'Merci de nous avoir contacter. Notre équipe va vous répondre dans les plus bref délais...');

            // envois mail ou base de donne
            $content = "<p>Bonjour, </p></br>";
            $content .= "<p>Vous avez reçus un message de <strong>".$form->getData()['prenom']." ".$form->getData()['nom']."</strong></p></br>";
            $content .= "<p>Adresse email : <strong>".$form->getData()['email']."</strong></p> </br>";
            $content .=  "<p>Message : ".$form->getData()['content']."</p></br></br>";

            $mail = new Mail();
            $mail->send("devphpsymfony@gmail.com", "ShopOccassion", "Demande de nouveau contact", $content);
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
