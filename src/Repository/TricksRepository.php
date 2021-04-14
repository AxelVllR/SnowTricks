<?php

namespace App\Repository;

use App\Entity\Tricks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tricks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tricks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tricks[]    findAll()
 * @method Tricks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TricksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tricks::class);
    }

    /**
     * @return int Returns an array of Tricks objects
     */
    public function countAll($filterGroup = null)
    {
        $query = $this->createQueryBuilder('t')
            ->select('count(t.id)');

        if(isset($filterGroup) && !empty($filterGroup)) {
            $query
                ->innerJoin('t.group_trick', 'g')
                ->where('g.id = :group')
                ->setParameter('group', $filterGroup);
        }


            return $query->getQuery()
            ->getSingleScalarResult();
        ;
    }

    /**
     * @return Tricks[] Returns an array of Tricks objects
     */
    public function findByPages($first, $perPage, $filterGroup = null)
    {
        $query = $this->createQueryBuilder('t')
            ->orderBy('t.created_at', 'DESC');

         if(isset($filterGroup) && !empty($filterGroup)) {
             $query
                 ->innerJoin('t.group_trick', 'g')
                 ->where('g.id = :group')
                 ->setParameter('group', $filterGroup);
         }

         return $query->setFirstResult($first)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();
        ;
    }

    /*
    public function findOneBySomeField($value): ?Tricks
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
