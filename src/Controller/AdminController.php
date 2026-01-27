<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    private EntityManagerInterface $em;
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->slugger = $slugger;
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
    public function showUser(int $id): Response
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

    #[Route('/catégories', name: 'categories')]
    public function categories(EntityManagerInterface $em)
    {
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('admin/category.html.twig', [
            'categories' => $categories,
            'active' => 'categories',
        ]);
    }

    #[Route('/catégories/créer', name: 'category_new')]
    public function newCategory(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugger->slug($category->getName())->lower();
            $category->setSlug($slug);
            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', 'Catégorie créée avec succès !');

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category_new.html.twig', [
            'form' => $form->createView(),
            'active' => 'categories',
        ]);
    }

    #[Route('catégorie/{slug}', name: 'category_show')]
    public function showCategory($slug, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = $this->em->getRepository(Category::class)->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($category->getName())->lower();
            $category->setSlug($slug);
            $em->flush();

            $this->addFlash('success', 'Catégorie mise à jour avec succès.');

            return $this->redirectToRoute('admin_category_show', [
                'slug' => $category->getSlug(),
            ]);
        }

        return $this->render('admin/category_show.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'active' => 'categories',
        ]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function deleteCategory(int $id, EntityManagerInterface $em): Response
    {
        // Récupérer la catégorie
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        // Supprimer la catégorie
        $em->remove($category);
        $em->flush();

        // Notif
        $this->addFlash('success', 'La catégorie a été supprimée avec succès.');

        return $this->redirectToRoute('admin_categories');
    }
}
