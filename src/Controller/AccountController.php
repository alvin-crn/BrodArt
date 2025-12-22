<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/compte', name: 'account')]
#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('', name: '')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            'active' => 'account',
            'metaTitle' => 'Mon compte'
        ]);
    }

    #[Route('/modifier-mon-mot-de-passe', name: '_password')]
    public function changePsw(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notif = null;

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPsw = $form->get('old_password')->getData();
            $newPsw = $form->get('new_password')->getData();

            if ($passwordHasher->isPasswordValid($user, $oldPsw)) {
                $hashed = $passwordHasher->hashPassword($user, $newPsw);
                $user->setPassword($hashed);

                $this->em->flush();

                $notif = "Votre mot de passe a bien été mis à jour.";
            } else {
                $notif = "Votre mot de passe actuel n'est pas le bon.";
            }
        }

        return $this->render('account/index.html.twig', [
            'formChangePassword' => $form->createView(),
            'notif' => $notif,
            'active' => 'editPsw',
            'metaTitle' => 'Changer mon mot de passe'
        ]);
    }
}
