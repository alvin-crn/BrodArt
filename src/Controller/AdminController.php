<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/utilisateur/{id}', name: 'user_show', methods: ['GET'])]
    public function userShow(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        return $this->render('admin/user_show.html.twig', [
            'user' => $user,
            'active' => 'user',
        ]);
    }

    #[Route('/user/{id}/update', name: 'user_update', methods: ['POST'])]
    public function updateUser(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // Prénom / Nom / Email
        $user->setFirstname($request->request->get('firstname'));
        $user->setLastname($request->request->get('lastname'));
        $user->setEmail($request->request->get('email'));

        // Admin toggle
        $isAdmin = $request->request->has('is_admin');
        $roles = $user->getRoles();
        if ($isAdmin && !in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
        }
        if (!$isAdmin) {
            $roles = array_diff($roles, ['ROLE_ADMIN']);
        }
        $user->setRoles(array_values($roles));

        // Désactivé / Blacklist toggles
        $user->setDesactived($request->request->has('desactived'));
        $user->setBlacklist($request->request->has('blacklist'));

        $em->flush();

        // Notification de succès
        $this->addFlash('success', 'Utilisateur mis à jour avec succès !');

        return $this->redirectToRoute('admin_user_show', [
            'id' => $user->getId()
        ]);
    }
}
