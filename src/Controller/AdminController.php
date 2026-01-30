<?php

namespace App\Controller;

use App\Entity\Size;
use App\Entity\User;
use App\Entity\Color;
use App\Entity\Order;
use App\Form\SizeType;
use App\Entity\Product;
use App\Form\ColorType;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Entity\ProductSize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        return $this->render('admin/user/user.html.twig', [
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

        return $this->render('admin/user/user_show.html.twig', [
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

        return $this->render('admin/category/category.html.twig', [
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

        return $this->render('admin/category/category_new.html.twig', [
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

        return $this->render('admin/category/category_show.html.twig', [
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

        return $this->render('admin/size/size.html.twig', [
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

        return $this->render('admin/size/size_new.html.twig', [
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

        return $this->render('admin/size/size_show.html.twig', [
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

    #[Route('/articles', name: 'products')]
    public function products(): Response
    {
        $products = $this->em->getRepository(Product::class)->findAll();

        return $this->render('admin/product/product.html.twig', [
            'products' => $products,
            'active' => 'products',
        ]);
    }

    #[Route('/article/nouvel-article', name: 'product_new')]
    public function createProduct(Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $sizes = $this->em->getRepository(Size::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            // Image principale
            $this->handleImage($form->get('illustration')->getData(), $product, 'setIllustration');

            // Images secondaires
            for ($i = 2; $i <= 10; $i++) {
                $this->handleImage($form->get('illustration' . $i)->getData(), $product, 'setIllustration' . $i);
            }

            // Slug automatique
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $product->setCreatedAt(new \DateTimeImmutable());

            $priceEuro = $form->get('price')->getData();
            $product->setPrice((int) round($priceEuro * 100));

            $promoEuro = $form->get('promo')->getData();
            if ($promoEuro !== null) {
                $product->setPromo((int) round($promoEuro * 100));
            }

            $deliveryEuro = $form->get('deliveryCost')->getData();
            $product->setDeliveryCost((int) round($deliveryEuro * 100));

            // Gestion tailles
            $sizesIds = $request->request->all('sizes', []);
            $stocks = $request->request->all('stocks', []);
            foreach ($sizesIds as $i => $sizeId) {
                $size = $this->em->getRepository(Size::class)->find($sizeId);
                if ($size && isset($stocks[$i])) {
                    $ps = new ProductSize();
                    $ps->setProduct($product);
                    $product->addProductSize($ps);
                    $ps->setSize($size);
                    $ps->setStock((int)$stocks[$i]);
                    $this->em->persist($ps);
                }
            }
            $this->em->persist($product);
            $this->em->flush();
            $this->addFlash('success', 'Produit créé avec succès !');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/product_new.html.twig', [
            'form' => $form->createView(),
            'sizes' => $sizes,
            'active' => 'products',
        ]);
    }

    #[Route('/article/{id}/modifier', name: 'product_show')]
    public function editProduct(Request $request, Product $product): Response
    {
        $product->setPrice($product->getPrice() / 100);
        $product->setPromo($product->getPromo() / 100);
        $product->setDeliveryCost($product->getDeliveryCost() / 100);

        $form = $this->createForm(ProductType::class, $product, [
            'edit' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($request->request->all());
            // === Supprimer les images existantes si demandé === 
            $imagesToRemove = $request->request->all('deleted_images');
            foreach ($imagesToRemove as $prop) {
                $filename = $product->{'get' . ucfirst($prop)}(); // Récupérer le nom de fichier actuel
                if (!$filename) {
                    continue;
                } // Si pas d'image, passer
                $path = $this->getParameter('kernel.project_dir') . '/public/media/product/' . $filename; // Construire le chemin complet
                if (file_exists($path)) {
                    unlink($path);
                } // Supprimer le fichier s'il existe
                $product->{'set' . ucfirst($prop)}(null); // Mettre à null dans l'entité
            }

            // === Gérer les nouvelles images uploadées ===
            for ($i = 0; $i <= 9; $i++) {
                $propForm = $i === 0 ? 'illustration' : 'illustration' . ($i + 1);
                /** @var UploadedFile|null $uploadedFile */
                $uploadedFile = $form->get($propForm)->getData();

                if (!$uploadedFile) {
                    continue;
                }

                // Chercher la première illustration vide en bdd
                $targetProp = null;
                for ($j = 0; $j <= 9; $j++) {
                    $checkProp = $j === 0 ? 'illustration' : 'illustration' . ($j + 1);
                    if ($product->{'get' . ucfirst($checkProp)}() === null) {
                        $targetProp = $checkProp;
                        break;
                    }
                }

                // S’il n’y a pas d'illustration de disponible, on skip
                if (!$targetProp) {
                    $this->addFlash('error', 'Impossible d’ajouter l’image : déjà 10 images maximum.');
                    continue;
                }

                // Appel de handleImage avec la première case vide trouvée
                $this->handleImage($uploadedFile, $product, 'set' . ucfirst($targetProp));
            }

            // === Convertir prix, promo, delivery_cost en centimes ===
            $product->setPrice((int)round($product->getPrice() * 100));
            if ($product->getPromo()) {
                $product->setPromo((int)round($product->getPromo() * 100));
            }
            $product->setDeliveryCost((int)round($product->getDeliveryCost() * 100));

            // === Gestion des tailles/stock ===
            // Supprimer les tailles retirées
            $deletedSizes = $request->request->all('deleted_sizes', []);
            foreach ($deletedSizes as $psId) {
                $productSize = $this->em->getRepository(ProductSize::class)->find($psId);
                if ($productSize && $productSize->getProduct()->getId() === $product->getId()) {
                    $product->removeProductSize($productSize);
                    $this->em->remove($productSize);
                }
            }

            // Mettre à jour le stock des tailles existantes
            $existingSizes = $request->request->all('existing-sizes', []);
            $existingStocks = $request->request->all('existing-stocks', []);
            foreach ($existingSizes as $i => $psId) {
                $productSize = $this->em->getRepository(ProductSize::class)->find($psId);
                if ($productSize && isset($existingStocks[$i])) {
                    $productSize->setStock((int) $existingStocks[$i]);
                    $this->em->persist($productSize);
                }
            }

            // Ajouter les nouvelles tailles/stocks
            $newSizes = $request->request->all('new-sizes', []);
            $newStocks = $request->request->all('new-stocks', []);
            foreach ($newSizes as $i => $sizeId) {
                $sizeEntity = $this->em->getRepository(Size::class)->find($sizeId);
                if ($sizeEntity && isset($newStocks[$i])) {
                    $ps = new ProductSize();
                    $ps->setProduct($product);
                    $ps->setSize($sizeEntity);
                    $ps->setStock((int) $newStocks[$i]);
                    $product->addProductSize($ps);
                    $this->em->persist($ps);
                }
            }

            $this->em->persist($product);
            $this->em->flush();

            $this->addFlash('success', 'Produit mis à jour avec succès');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/product_show.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'sizes' => $this->em->getRepository(Size::class)->findAll(),
            'active' => 'products',
        ]);
    }

    private function handleImage(?UploadedFile $file, Product $product, string $setter): void
    {
        if (!$file) {
            return;
        }

        $filename = uniqid() . '.' . $file->guessExtension();

        $file->move(
            $this->getParameter('kernel.project_dir') . '/public/media/product',
            $filename
        );

        $product->$setter($filename);
    }

    #[Route('/commandes', name: 'orders')]
    public function orders(): Response
    {
        return $this->render('admin/order/order.html.twig', [
            'orders' => $this->em->getRepository(Order::class)->findAll(),
            'active' => 'orders',
        ]);
    }

    #[Route('/commandes/{reference}', name: 'order_show')]
    public function orderShow(string $reference): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        return $this->render('admin/order/order_show.html.twig', [
            'order' => $order,
            'active' => 'orders',
        ]);
    }

    #[Route('/commande/{id}/state', name: 'order_update_state', methods: ['POST'])]
    public function updateOrderState(Order $order, Request $request): Response
    {
        $state = (int) $request->request->get('state');

        $order->setState($state);
        $this->em->flush();

        $this->addFlash('success', 'Statut de la commande mis à jour');

        return $this->redirectToRoute('admin_order_show', [
            'reference' => $order->getReference()
        ]);
    }
}
