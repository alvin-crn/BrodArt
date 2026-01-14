<?php

namespace App\Repository;

use App\Service\SearchService;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[]
     */
    public function findWithSearch(SearchService $search)
    {
        $query = $this
            ->createQueryBuilder('p') //p pour product
            ->select('cat', 'p', 'color', 'productSizes') // cat pour catégorie & p pour product & color pour color & productSizes pour size
            ->join('p.category', 'cat') //jointure entre produit de cat et cat
            ->join('p.color', 'color') //jointure entre produit de color et color
            ->join('p.productSizes', 'productSizes'); //jointure entre produit de productsize et size
        
        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('cat.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }

        if (!empty($search->couleurs)) {
            $query = $query
                ->andWhere('color.id IN (:couleurs)')
                ->setParameter('couleurs', $search->couleurs);
        }

        if (!empty($search->tailles)) {
            foreach ($search->tailles as $taille) {
                $array_sizes[] = $taille->getProductSizes()->getValues();
            }
            foreach ($array_sizes as $sizes_group) {
                foreach ($sizes_group as $size) {
                    $AllSizes[] = $size;
                }
            }
            $query = $query
                ->andWhere('productSizes.id IN (:AllSizes)')
                ->setParameter('AllSizes', $AllSizes);
        }

        if (!empty($search->string)) {
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%");
        }

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
