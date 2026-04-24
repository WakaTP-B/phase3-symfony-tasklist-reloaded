<?php

namespace App\Repository;

use App\Entity\Priority;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Priority>
 */
class PriorityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Priority::class);
    }

       /**
        * @return Priority[] Returns an array of Priority objects
        */
       public function findAvailableForUser(User $user): array
       {
           return $this->createQueryBuilder('p')
               ->Where('p.User IS NULL OR p.User = :user')
               ->setParameter('user', $user)
               ->orderBy('p.User', 'ASC')
               ->addOrderBy('p.id', 'ASC')
               ->getQuery()
               ->getResult()
           ;
       }

    //    public function findOneBySomeField($value): ?Priority
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
