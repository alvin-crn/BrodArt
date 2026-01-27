<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'active' => 'dashboard',
        ]);
    }

    #[Route('/utilisateur', name: 'user')]
    public function user(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->render('admin/user.html.twig', [
            'users' => $users,
            'active' => 'user',
        ]);
    }
}
