<?php

namespace App\Repository;

use App\Enum\TaskStatus;
use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findTasksSorted(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect(
                'CASE
                    WHEN t.status = :pending   THEN 0
                    WHEN t.status = :completed THEN 1
                    WHEN t.status = :archived  THEN 2
                    ELSE 3
                END AS HIDDEN statusOrder'
            )
            ->where('t.User = :user')
            ->setParameter('user', $user)
            ->setParameter('pending',   TaskStatus::PENDING)
            ->setParameter('completed', TaskStatus::COMPLETED)
            ->setParameter('archived',  TaskStatus::ARCHIVED)
            ->orderBy('t.isPinned', 'DESC')
            ->addOrderBy('statusOrder', 'ASC')
            ->addOrderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
