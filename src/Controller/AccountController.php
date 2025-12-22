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

}
