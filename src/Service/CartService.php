<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartService extends AbstractController
{
    private $session;
    private $em;

    public function __construct(RequestStack $stack, EntityManagerInterface $em)
    {
        $this->session = $stack->getSession();
        $this->em = $em;
    }

    public function add($id, $variant, $quantity, $personalizedPicName, $size, $idSize)
    {
        $idInt = intval($id);
        $quantityInt = intval($quantity);

        $cart = $this->session->get('cart', []);

        $cart[$variant] = [
            'id' => $idInt,
            'size' => $size,
            'id_size' => $idSize,
            'quantity' => $quantityInt,
            'personalized_pic_name' => $personalizedPicName,
        ];

        $this->session->set('cart', $cart);
    }

    public function getCart()
    {
        return $this->session->get('cart');
    }

    public function removeCartAndPic()
    {
        $cartComplete = $this->getFullCart();


        foreach ($cartComplete as $product) {
            $filename = $product['photo_client'];

            if ($filename) {
                $path = $this->getParameter('UPLOAD_CLIENT_PHOTO_DIR') . '/' . $filename;

                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        return $this->session->remove('cart');
    }


    public function delete($variant)
    {
        $cart = $this->session->get('cart', []);
        $product = $cart[$variant];

        $filename = $product['personalized_pic_name'];
        if ($filename) {
            $path = $this->getParameter('UPLOAD_CLIENT_PHOTO_DIR') . '/' . $filename;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        unset($cart[$variant]);

        return $this->session->set('cart', $cart);;
    }

    public function getFullCart()
    {
        $cartComplete = [];

        $panier = $this->getCart();

        if ($panier) {
            foreach ($panier as $variant => ['id' => $id, 'size' => $size, 'quantity' => $quantity, 'personalized_pic_name' => $personalizedPicName, 'id_size' => $idSize]) {
                $productObject = $this->em->getRepository(Product::class)->findOneById($id);
                if (!$productObject) {
                    $this->delete($variant);
                    continue;
                }

                $cartComplete[] = [
                    'variant' => $variant,
                    'product' => $productObject,
                    'size' => $size,
                    'id_size' => $idSize,
                    'quantity' => $quantity,
                    'photo_client' => $personalizedPicName,
                ];
            }
        }

        return $cartComplete;
    }
}
