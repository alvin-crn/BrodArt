<?php

namespace App\Controller;

use App\Entity\Order;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Form\PaymentType;
use App\Entity\ProductSize;
use App\Entity\OrderDetails;
use App\Service\CartService;
use App\Service\MailService;
use App\Service\PersonalizedPicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/commande', name: 'order')]
    public function index(CartService $cart): Response
    {
        if (!$cart->getCart()) {
            return $this->redirectToRoute('cart');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFullCart(),
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap', methods: "POST")]
    public function add(CartService $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carrier = filter_input(INPUT_POST, 'carrier', FILTER_SANITIZE_SPECIAL_CHARS); // Frais de port
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS); // Commentaire client
            $date = new DateTimeImmutable(); // Date du jour
            $delivery = $form->get('addresses')->getData(); // Adresse de livraison

            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $delivery_content .= '<br/>' . $delivery->getPhone();
            if ($delivery->getCompagny()) {
                $delivery_content .= '<br/>' . $delivery->getCompagny();
            }
            $delivery_content .= '<br/>' . $delivery->getAddress();
            $delivery_content .= '<br/>' . $delivery->getPostal() . ' - ' . $delivery->getCity() . ' - ' . $delivery->getCountry();

            // Enregister la commande
            $order = new Order();
            $ref = $date->format('dmY') . '-' . uniqid(); // N° de commande date + UniqueID
            $order->setReference($ref);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierPrice($carrier);
            $order->setDelivery($delivery_content);
            if ($comment) {
                $order->setComment($comment);
            }
            $order->setState(0);

            $this->em->persist($order);

            // Enregister les produits associé à la commande
            $i = 1;
            foreach ($cart->getFullCart() as $product) {
                if ($product['product']->getPromo()) {
                    $product_price = $product['product']->getPromo();
                } else {
                    $product_price = $product['product']->getPrice();
                }

                $pathFrom = $this->getParameter('UPLOAD_CLIENT_PHOTO_DIR') . '/' . $product['photo_client']; // Ancien emplacement
                $extension = pathinfo($product['photo_client'], PATHINFO_EXTENSION); // Extension du fichier
                $newFilename = $ref . '-' . $i . '-' . $delivery->getFirstname() . '-' . $delivery->getLastname() . '.' . $extension; // Nouveau nom : Ref-1-Prenom-Nom.extension
                $pathTo = $this->getParameter('UPLOAD_UNVALIDED_ORDER_PHOTO_DIR') . '/' . $newFilename; // Nouvel emplacement
                rename($pathFrom, $pathTo); // Déplacement
                $i++; // Numéro photo+1

                $productSize = $this->em->getRepository(ProductSize::class)->find($product['id_size']);

                $orderDetails = new OrderDetails();
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setSize($product['size']);
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product_price);
                $orderDetails->setTotal($product_price * $product['quantity']);
                $orderDetails->setPhotoClient($newFilename);
                $orderDetails->setProductSize($productSize);

                $order->addOrderDetail($orderDetails);

                $this->em->persist($orderDetails);
            }

            $this->em->flush();

            $quantity = null;
            foreach ($cart->getFullCart() as $product) {
                $quantityInt = intval($product['quantity']);
                $quantity = $quantity + $quantityInt; // Quantité total
            }

            $cart->removeCart(); // Suppresion du panier
            $order = $this->em->getRepository(Order::class)->findOneByReference($ref); // On récupère l'entierté de la commande que l'on vient d'injecter

            return $this->render('order/recap.html.twig', [
                'order' => $order,
                'quantity' => $quantity
            ]);
        }

        return $this->redirectToRoute('cart');
    }

    #[Route('/commande/paiement/{ref}', name: 'order_payment')]
    public function payment($ref, Request $request): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByReference($ref);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('cart');
        }

        if ($order->getState() != 0) {
            return $this->redirectToRoute('account_orders');
        }

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cardData = [
                'name'   => $form->get('card_name')->getData(),
                'number' => $form->get('card_number')->getData(),
                'expiry' => $form->get('card_expiry')->getData(),
                'cvc'    => $form->get('card_cvc')->getData(),
            ];

            if ($cardData['number'] == "1234 5678 9012 3456" && $cardData['expiry'] == "02/29" && $cardData['cvc']  == "123") {
                $request->getSession()->set('payment_allowed', true);
                return $this->redirectToRoute('order_validate', ['ref' => $order->getReference()]);
            } else {
                return $this->redirectToRoute('order_cancel', ['ref' => $order->getReference()]);
            }
        }

        return $this->render('order/payment.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/merci/{ref}', name: 'order_validate')]
    public function validateOrder($ref, Request $request): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByReference($ref);
        $allowed = $request->getSession()->get('payment_allowed', false);

        if (!$order || $order->getUser() != $this->getUser() || $allowed != true || $order->getState() != 0) {
            return $this->redirectToRoute('cart');
        }

        $order->setState(1);
        $this->em->flush();

        // Déplacer les photos et changer le stock des articles
        foreach ($order->getOrderDetails() as $detail) {
            $pathFrom = $this->getParameter('UPLOAD_UNVALIDED_ORDER_PHOTO_DIR') . '/' . $detail->getPhotoClient(); // Ancien emplacement
            $pathTo = $this->getParameter('UPLOAD_VALIDED_ORDER_PHOTO_DIR') . '/' . $detail->getPhotoClient(); // Nouvel emplacement
            rename($pathFrom, $pathTo); // Déplacement

            $sizeStock = $this->em->getRepository(ProductSize::class)->findOneById($detail->getProductSize());
            $stock = $sizeStock->getStock();
            $newStock = ($stock - 1);
            $sizeStock->setStock($newStock);
            $this->em->flush();
        }

        $request->getSession()->remove('payment_allowed');

        // Envoyer un email au client pour confirmer la commande
        $mail = new MailService();
        $content = "Nous vous remercions pour votre commande n°<strong>" . $order->getReference() . "</strong>. <br><br> Vous serez livré à l'adresse suivante :<br><i>" . $order->getDelivery() . "</i>";
        $mail->sendEmail($order->getUser()->getEmail(), 'Confirmation de votre commande Brod\'Art n°'. $order->getReference(), $content);

        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/commande/echec/{ref}', name: 'order_cancel')]
    public function canceledOrder($ref)
    {
        $order = $this->em->getRepository(Order::class)->findOneByReference($ref);

        if (!$order || $order->getUser() != $this->getUser() || $order->getState() != 0) {
            return $this->redirectToRoute('cart');
        }
        
        return $this->render('order/cancel.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/unvalided-order-client-photo/{filename}', name: 'unvalided_order_client_photo')]
    public function clientPhoto(string $filename, PersonalizedPicService $personalizedPicService): BinaryFileResponse
    {
        return $personalizedPicService->getUnvalidedOrderPhoto($filename);
    }
}
