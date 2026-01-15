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

    #[Route('/meilleure-vente', name: 'best_sale')]
    public function bestSale(Request $request): Response
    {
        $search = New SearchService;
        $formSearch = $this->createForm(SearchType::class, $search);

        $formSearch->handleRequest($request);
        
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $allProducts = $this->em->getRepository(Product::class)->findWithSearch($search);
            foreach ($allProducts as $product) {
                if ($product->getIsBest() == true){
                    $bestProducts[] = $product;
                }
            }

            if (empty($bestProducts)) {
                $bestProducts = [];
            }
        } else {
            $bestProducts = $this->em->getRepository(Product::class)->findByIsBest(1);

            if (empty($bestProducts)) {
                $bestProducts = [];
            }
        }

        return $this->render('product/index.html.twig', [
            'products' => $bestProducts,
            'formSearch' => $formSearch->createView(),
        ]);
    }

    #[Route('/nouveauté', name: 'new_product')]
    public function newProduct(Request $request): Response
    {
        $search = New SearchService;
        $formSearch = $this->createForm(SearchType::class, $search);

        $formSearch->handleRequest($request);

        $now = new DateTimeImmutable();

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $allProducts = $this->em->getRepository(Product::class)->findWithSearch($search);
            foreach ($allProducts as $product) {
                if ($now < $product->getCreatedAt()->modify('+ 1 month')) {
                    $newProducts[] = $product;
                }
            }

            if (empty($newProducts)) {
                $newProducts = [];
            }
        } else {
            $allProducts = $this->em->getRepository(Product::class)->findAll();
            foreach ($allProducts as $product) {
                if ($now < $product->getCreatedAt()->modify('+ 1 month')) {
                    $newProducts[] = $product;
                }
            }

            if (empty($newProducts)) {
                $newProducts = [];
            }
        }

        return $this->render('product/index.html.twig', [
            'products' => $newProducts,
            'formSearch' => $formSearch->createView(),
        ]);
    } 

    #[Route('/categorie/{category}', name: 'product_by_category')]
    public function categorie($category, Request $request): Response
    {
        $categorie = $this->em->getRepository(Category::class)->findOneByName($category);

        if(!$categorie) {
            return $this->redirectToRoute('products');
        }

        $search = New SearchService;
        $formSearch = $this->createForm(SearchType::class, $search);

        $formSearch->handleRequest($request);
        
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $allProducts = $this->em->getRepository(Product::class)->findWithSearch($search);
            foreach ($allProducts as $product) {
                if ($product->getCategory()->getId() == $categorie->getId()) {
                    $productByCategory[] = $product;
                }
            }

            if (empty($productByCategory)) {
                $productByCategory = [];
            }
        } else {
            $productByCategory = $this->em->getRepository(Product::class)->findByCategory($categorie->getId());

            if (empty($productByCategory)) {
                $productByCategory = [];
            }
        }

        return $this->render('product/index.html.twig', [
            'notCategory' => 'notCategory',
            'products' => $productByCategory,
            'formSearch' => $formSearch->createView(),
        ]);
    }
}
