<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 *
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]   findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);

    }//end __construct()


    public function save(Products $entity, bool $flush=false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

    }//end save()


    public function remove(Products $entity, bool $flush=false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

    }//end remove()

    public function findBySearchAndCategory(string $searchTerm, string $category)
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($searchTerm)) {
            $qb->andWhere('p.name LIKE :searchTerm')->setParameter('searchTerm', '%'.$searchTerm.'%');
        }

        if (!empty($category)) {
            $qb->andWhere('p.category = :category')->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();

    }//end findBySearchAndCategory()


    // **
    // * @return Products[] Returns an array of Products objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('p')
    // ->andWhere('p.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('p.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?Products
    // {
    // return $this->createQueryBuilder('p')
    // ->andWhere('p.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }
}//end class
