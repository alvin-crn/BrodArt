<?php

namespace App\Controller;

use App\Entity\ProductSize;
use App\Service\CartService;
use App\Service\PersonalizedPicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/mon-panier', name: 'cart')]
    public function index(CartService $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFullCart(),
        ]);
    }

    #[Route('/cart/add/{id}/{variant}', name: 'add_to_cart')]
    public function add(Request $request, CartService $cart, $id, $variant)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Gestion de la photo
        /** @var UploadedFile|null $photo */
        $photo = $request->files->get('photo_client');

        if ($photo instanceof UploadedFile) {
            $safeFilename = uniqid() . '.' . $photo->guessExtension(); // Nom final
            $photo->move($this->getParameter('UPLOAD_CLIENT_PHOTO_DIR'), $safeFilename); // Déplacement vers var/uploads/personalized_pic
        } else {
            $safeFilename = $request->query->get('photo'); // Cas où la photo est déjà existante (ex : modification panier)
        }

        // Gestion de la taille
        if ($request->request->get('size')) {
            $idSize = $request->request->get('size');
            $sizeStock = $this->em->getRepository(ProductSize::class)->find($idSize);
            $size = $sizeStock?->getSize()?->getSize();
        } else {
            $size = $request->query->get('size');
            $idSize = $request->query->get('id_size');
        }

        // Gestion de la quantité
        $quantity = (int) ($request->query->get('quantity') ?? 1);

        if ($quantity > 0) {
            $cart->add($id, $variant, $quantity, $safeFilename, $size, $idSize);
        } else {
            $cart->delete($variant);
        }

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove', name: 'remove_cart')]
    public function remove(CartService $cart)
    {
        $cart->removeCartAndPic();

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/delete/{variant}', name: 'delete_to_cart')]
    public function delete(CartService $cart, $variant)
    {
        $cart->delete($variant);

        return $this->redirectToRoute('cart');
    }

    #[Route('/client-photo/{filename}', name: 'client_photo')]
    public function clientPhoto(string $filename, PersonalizedPicService $personalizedPicService ): BinaryFileResponse 
    {
        return $personalizedPicService->getPersonalizedPic($filename);
    }
}
