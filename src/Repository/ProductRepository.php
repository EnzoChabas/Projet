<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
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


    public function findBySearch(string $search): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->andWhere('p.name LIKE :val OR p.description LIKE :val OR c.name LIKE :val')
            ->setParameter('val', "%$search%")
            ->getQuery()
            ->getResult();
    }

    public function findByGroup($id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :id')
            ->setParameter('id', $id)
            ->groupBy('p.name')
            ->getQuery()
            ->getResult();
    }


}
