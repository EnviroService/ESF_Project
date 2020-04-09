<?php

namespace App\Repository;

use App\Entity\RateCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

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

    /**
     * @return RateCard[]
     */
    public function enumerate(): array
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder
            ->select('e')
            ->from($this->getClassName(), 'e')
        ;

        return $builder->getQuery()->getResult();
    }

    /**
    * @return RateCard[] Returns an array of RateCard objects
    */
    public function findAllSolutionDistinct()
    {
        return $this->createQueryBuilder('r')
            ->select('r.solution')
            ->orderBy('r.solution', 'ASC')
            ->distinct(true)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $solution
     * @return RateCard[] Returns an array of RateCard objects
     */
    public function findAllBrandDistinct($solution)
    {
        return $this->createQueryBuilder('r')
            ->select('r.brand')
            ->orderBy('r.brand', 'ASC')
            ->where('r.solution = :data')
            ->setParameter('data', $solution)
            ->distinct(true)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $solution
     * @param $brand
     * @return RateCard[] Returns an array of RateCard objects
     */
    public function findAllModelsDistinct($solution, $brand)
    {
        return $this->createQueryBuilder('r')
            ->select('r.models')
            ->orderBy('r.models', 'ASC')
            ->where('r.solution = :solution')
            ->andWhere('r.brand = :brand')
            ->setParameter('solution', $solution)
            ->setParameter('brand', $brand)
            ->distinct(true)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $solution
     * @param $brand
     * @param $model
     * @return RateCard[] Returns an array of RateCard objects
     */
    public function findAllPrestationsDistinct($solution, $brand, $model)
    {
        return $this->createQueryBuilder('r')
            ->select('r.prestation')
            ->orderBy('r.prestation', 'ASC')
            ->where('r.solution = :solution')
            ->andWhere('r.brand = :brand')
            ->andWhere('r.models = :model')
            ->setParameter('solution', $solution)
            ->setParameter('brand', $brand)
            ->setParameter('model', $model)
            ->distinct(true)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $solution
     * @param $brand
     * @param $model
     * @param $prestation
     * @return RateCard[] Returns an array of RateCard objects
     * @throws NonUniqueResultException
     */
    public function findLineRateCard($solution, $brand, $model, $prestation)
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.models', 'ASC')
            ->where('r.solution = :solution')
            ->andWhere('r.brand = :brand')
            ->andWhere('r.models = :model')
            ->andWhere('r.prestation = :prestation')
            ->setParameter('solution', $solution)
            ->setParameter('brand', $brand)
            ->setParameter('model', $model)
            ->setParameter('prestation', $prestation)
            ->distinct(true)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /*public function selectAllUnique(RateCardRepository $rr){
        $test = $rr->findAll();
        $models = [];
        $result = "il n'y a aucun téléphone en base de donnée, veuillez mettre à jour la matrice.";
        if (!empty($test)){
            foreach ($test as $model) {
                $marque = $model->getModels();
                array_push($models, $marque);
                $result = array_unique($models);
            }
        }

        return $result;
    }*/

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
