<?php

namespace App\Repository;

use App\Entity\GiftGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GiftGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method GiftGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method GiftGroup[]    findAll()
 * @method GiftGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GiftGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GiftGroup::class);
    }

    // /**
    //  * @return GiftGroup[] Returns an array of GiftGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GiftGroup
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
