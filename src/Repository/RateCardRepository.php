<?php

namespace App\Repository;

use App\Entity\RateCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RateCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method RateCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method RateCard[]    findAll()
 * @method RateCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RateCard::class);
    }

    // /**
    //  * @return RateCard[] Returns an array of RateCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RateCard
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
