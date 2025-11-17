<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use App\Form\RegisterType;
use App\Service\MailService;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
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
    public function register(Request $request): Response
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

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function forgotPassword(Request $request, MailService $mailService): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }


        if ($request->get('email')) {
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));
            if ($user) {
                // Enregistrer en base la demande de reset_password avec user, token, createdAt.
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new DateTimeImmutable());
                $this->em->persist($reset_password);
                $this->em->flush();

                // Envoyer un mail à l'utilisateur avec un lien lui permettant de mettre à jour son MDP
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken(),
                ]);
                
                $subject = 'Réinitialiser votre mot de passe sur Brod\'Art';
                $content = "Vous avez demandé à réinitialiser votre mot de passe sur le site Brod'Art<br/><br/>Merci de bien vouloir cliquer sur le lien suivant afin de <a href='" . $url . "'>mettre à jour votre mot de passe</a>.";
                $mailService->sendEmail($user->getEmail(), $subject, $content);

                $this->addFlash('notif', 'Vous allez recevoir dans quelques secondes un mail avec la procédure pour réinitialiser votre mot de passe. Si ce n\'est pas le cas pensez à vérifier vos spams');
            } else {
                $this->addFlash('notif', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'update_password')]
    public function updatePassword(Request $request, $token)
    {
        $reset_password = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);

        // Vérifier si la demande existe.
        if (!$reset_password) {
            $this->addFlash('notif', 'Demande de réinitialisation inconnue, veuillez réessayer avec un nouveau lien.');
            return $this->redirectToRoute('reset_password');
        }

        // Verifier si la demande a été créée il y a moins de 30 min.
        $now = new DateTimeImmutable();
        if ($now > $reset_password->getCreatedAt()->modify('+ 30 minute')) {
            $this->addFlash('notice', 'Votre demande de nouveau mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPsw = $form->get('password')->getData();
            // Encodage du MDP
            $password = password_hash($newPsw, PASSWORD_DEFAULT);
            $reset_password->getUser()->setPassword($password);
            // Flush à la BDD
            $this->em->flush();
            // Redirection vers la page de connexion
            $this->addFlash('notif', 'Votre mot de passe a bien été mise à jour.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
