<?php

namespace App\Controller;

use App\Service\SearchService;
use DateTimeImmutable;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/nos-articles', name: 'products')]
    public function index(Request $request): Response
    {
        $search = New SearchService;
        $formSearch = $this->createForm(SearchType::class, $search);

        $formSearch->handleRequest($request);
        
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $products = $this->em->getRepository(Product::class)->findWithSearch($search);
        } else {
            $products = $this->em->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'formSearch' => $formSearch->createView(),
        ]);
    }

    #[Route('/produit/{slug}', name: 'product')]
    public function show($slug): Response
    {
        $product = $this->em->getRepository(Product::class)->findOneBySlug($slug);

        if(!$product) {
            return $this->redirectToRoute('products');
        }

        $sizes = $product->getProductSizes()->getValues();

        $variant = rand(1000000000, 9999999999);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'sizes' => $sizes,
            'variant' => $variant,
        ]);
    }

}
