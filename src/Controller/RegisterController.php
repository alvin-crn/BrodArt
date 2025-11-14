<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    #[Route('/inscription', name: 'register')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }
        
        $notif = null;
        $user = new User();
        $formRegister = $this->createForm(RegisterType::class, $user);
        $formRegister->handleRequest($request);
        
        if ($formRegister->isSubmitted() && $formRegister->isValid()) {
            // Récuperer les données du formulaire
            $user = $formRegister->getData();
            $user->setDesactived(0);
            $user->setBlacklist(0);
            
            $search_email = $this->em->getRepository(User::class)->findOneByEmail($user->getEmail()); // Verifier s'il est pas deja dans la BDD

            if (!$search_email) {
                // Encoder le mot de passe
                $password = password_hash($user->getPassword(), PASSWORD_DEFAULT);
                $user->setPassword($password);
                // Envoyer à la base de donnée
                $this->em->persist($user);
                $this->em->flush();

                // Envoyer un mail
                /*
                $mail = new Mail();
                $content = "Bienvenue sur Brod'Art, <br><br> Nous vous remercions pour votre inscription. <br><br> Vous pouvez dès à present vous connecter sur votre espace client.";
                $mail->send($user->getEmail(), $user->getFirstname(), 'Confirmation d\'inscription sur Brod\'Art', $content, $this->getParameter('app.mailjet_apikey'), $this->getParameter('app.mailjet_apikey_secret'));
                */

                $notif = "Votre inscription a bien été prise en compte.";
            } else {
                $notif = "L'email renseigné est déjà utilisé.";
            }
        }

        return $this->render('register/index.html.twig', [
            'formRegister' => $formRegister->createView(),
            'notif' => $notif,
        ]);
    }
}
