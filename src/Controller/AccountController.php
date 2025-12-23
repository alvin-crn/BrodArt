<?php

namespace App\Controller;

use App\Entity\Order;
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

    #[Route('/mes-adresses', name: '_addresses')]
    public function myAddresses(): Response
    {
        return $this->render('account/index.html.twig', [
            'active' => 'addresses',
            'metaTitle' => 'Mes adresses'
        ]);
    }

    #[Route('/ajouter-une-adresse', name: '_address_add')]
    public function addAddress(Request $request)
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->em->persist($address);
            $this->em->flush();
            return $this->redirectToRoute('account_addresses');
            // TO-DO : Rediriger vers le procéssuce de commande si on ajoute une adresse à ce moment là
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
            'active' => 'address_add',
            'metaTitle' => 'Ajouter une adresse'
        ]);
    }

    #[Route('/modifier-une-adresse/{id}', name: '_address_edit')]
    public function editAddress($id, Request $request): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);
        if(!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('account_addresses');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
            'active' => 'address_edit',
            'metaTitle' => 'Modifier une adresse'
        ]);
    }

    #[Route('/supprimer-une-adresse/{id}', name: '_address_delete')]
    public function deleteAddress($id)
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);
        if($address && $address->getUser() == $this->getUser()) {
            $this->em->remove($address);
            $this->em->flush();
        }

        return $this->redirectToRoute('account_addresses', [
            'active' => 'addresses',
            'metaTitle' => 'Mes adresses'
        ]);
    }

    #[Route('/mes-commandes', name: '_orders')]
    public function myOrders(): Response
    {
        $orders = $this->em->getRepository(Order::class)->findSuccessOrders($this->getUser());
       
        return $this->render('account/index.html.twig', [
            'orders' => $orders,
            'active' => 'orders',
            'metaTitle' => 'Mes commandes'
        ]);
    }

    #[Route('/mes-commandes/{ref}', name: '_order_detail')]
    public function orderDetail($ref): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByReference($ref);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_orders');
        }

        $metaTitle = "Commande n°".$order->getReference();
       
        return $this->render('account/index.html.twig', [
            'order' => $order,
            'active' => 'order_detail',
            'metaTitle' => $metaTitle
        ]);
    }

}
