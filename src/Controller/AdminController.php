<?php

namespace App\Controller;

use App\Entity\Size;
use App\Entity\User;
use App\Entity\Color;
use App\Form\SizeType;
use App\Form\ColorType;
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
    public function updateUser(User $user, Request $request): Response
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

        $this->em->flush();

        // Notification de succès
        $this->addFlash('success', 'Utilisateur mis à jour avec succès !');

        return $this->redirectToRoute('admin_user_show', [
            'id' => $user->getId()
        ]);
    }

    #[Route('/catégories', name: 'categories')]
    public function categories()
    {
        $categories = $this->em->getRepository(Category::class)->findAll();

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
    public function showCategory($slug, Request $request, SluggerInterface $slugger): Response
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
            $this->em->flush();

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
    public function deleteCategory(int $id): Response
    {
        // Récupérer la catégorie
        $category = $this->em->getRepository(Category::class)->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        // Supprimer la catégorie
        $this->em->remove($category);
        $this->em->flush();

        // Notif
        $this->addFlash('success', 'La catégorie a été supprimée avec succès.');

        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/tailles', name: 'sizes')]
    public function sizes()
    {
        $sizes = $this->em->getRepository(Size::class)->findAll();

        return $this->render('admin/size.html.twig', [
            'sizes' => $sizes,
            'active' => 'sizes',
        ]);
    }

    #[Route('/taille/créer', name: 'size_new')]
    public function newSize(Request $request): Response
    {
        $size = new Size();
        $form = $this->createForm(SizeType::class, $size);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($size);
            $this->em->flush();

            $this->addFlash('success', 'Taille créée avec succès !');

            return $this->redirectToRoute('admin_sizes');
        }

        return $this->render('admin/size_new.html.twig', [
            'form' => $form->createView(),
            'active' => 'sizes',
        ]);
    }

    #[Route('taille/{id}', name: 'size_show')]
    public function showSize(Size $size, Request $request): Response
    {
        $form = $this->createForm(SizeType::class, $size);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Taille mise à jour avec succès.');

            return $this->redirectToRoute('admin_size_show', [
                'id' => $size->getId()
            ]);
        }

        return $this->render('admin/size_show.html.twig', [
            'size' => $size,
            'form' => $form->createView(),
            'active' => 'sizes',
        ]);
    }

    #[Route('/size/delete/{id}', name: 'size_delete')]
    public function deleteSize(int $id): Response
    {
        // Récupérer la taille
        $size = $this->em->getRepository(Size::class)->find($id);

        if (!$size) {
            throw $this->createNotFoundException('Taille introuvable');
        }

        // Supprimer la taille
        $this->em->remove($size);
        $this->em->flush();

        // Notif
        $this->addFlash('success', 'La taille a été supprimée avec succès.');
        return $this->redirectToRoute('admin_sizes');
    }

    #[Route('/couleurs', name: 'colors')]
    public function colors()
    {
        $colors = $this->em->getRepository(Color::class)->findAll();

        return $this->render('admin/color/color.html.twig', [
            'colors' => $colors,
            'active' => 'colors',
        ]);
    }

    #[Route('/couleur/créer', name: 'color_new')]
    public function newColor(Request $request): Response
    {
        $color = new Color();
        $form = $this->createForm(ColorType::class, $color);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($color);
            $this->em->flush();

            $this->addFlash('success', 'Couleur créée avec succès !');

            return $this->redirectToRoute('admin_colors');
        }

        return $this->render('admin/color/color_new.html.twig', [
            'form' => $form->createView(),
            'active' => 'colors',
        ]);
    }

    #[Route('couleur/{id}', name: 'color_show')]
    public function showColor(Color $color, Request $request): Response
    {
        $form = $this->createForm(ColorType::class, $color);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Couleur mise à jour avec succès.');

            return $this->redirectToRoute('admin_color_show', [
                'id' => $color->getId()
            ]);
        }

        return $this->render('admin/color/color_show.html.twig', [
            'color' => $color,
            'form' => $form->createView(),
            'active' => 'colors',
        ]);
    }

    #[Route('/color/delete/{id}', name: 'color_delete')]
    public function deleteColor(int $id): Response
    {
        // Récupérer la couleur
        $color = $this->em->getRepository(Color::class)->find($id);

        if (!$color) {
            throw $this->createNotFoundException('Couleur introuvable');
        }

        // Supprimer la couleur
        $this->em->remove($color);
        $this->em->flush();

        // Notif
        $this->addFlash('success', 'La couleur a été supprimée avec succès.');
        return $this->redirectToRoute('admin_colors');
    }
}
