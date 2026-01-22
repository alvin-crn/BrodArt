<?php

namespace App\Controller;

use App\Entity\Header;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        //  CARROUSEL
        $headers = $this->em->getRepository(Header::class)->findAll();

        // BESTSELLER
        $bestProducts = $this->em->getRepository(Product::class)->findByIsBest(1);

        if (empty($bestProducts)) {
            $bestProducts = [];
        }

        // CATEGORIES
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('home/index.html.twig', [
            'headers' => $headers,
            'bestseller' => $bestProducts,
            'categories' => $categories,
        ]);
    }

    #[Route('/ajax/categories', name: 'ajax_categories', methods: ['GET'])]
    public function ajaxCategories(): JsonResponse
    {
        $categories = $this->em->getRepository(Category::class)->findBy([], ['name' => 'ASC']);

        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'name' => $category->getName(),
                'slug' => $category->getSlug(),
            ];
        }

        return new JsonResponse($data);
    }
}
