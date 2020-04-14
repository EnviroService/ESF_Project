<?php

namespace App\Repository;

use App\Entity\RateCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

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

    public function selectBrandUnique(){
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder
            ->select('b.brand')
            ->from($this->getClassName(), 'b')
            ->distinct(true);

        return $builder->getQuery()->getResult();
    }

    public function getModelByBrand(string $brand){
        $builder = $this->createQueryBuilder('b');
        $builder
            ->select('b.models')
            ->where('b.brand = :brand')
            ->setParameter('brand', $brand);

        return $builder->getQuery()->getResult();
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
