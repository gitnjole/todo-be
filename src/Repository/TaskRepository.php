<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
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

    /**
     * 
     * @param \App\Entity\User $user
     * 
     * @return Task[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user_task = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function create(Task $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    public function update(Task $task): void
    {
        $this->getEntityManager()->flush();
    }

    public function delete(Task $task): void
    {
        $this->getEntityManager()->remove($task);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete tasks older than a specified date
     * 
     * @param \DateTimeInterface $date
     * @return int
     */
    public function deleteTasksOlderThan(\DateTimeInterface $date): int
    {
        if (!$date instanceof \DateTimeImmutable) {
            $date = new \DateTimeImmutable($date->format('Y-m-d H:i:s'));
        }

        $queryBuilder = $this->createQueryBuilder('t')
            ->delete()
            ->where('t.created_at < :date')
            ->setParameter('date', $date);

        $query = $queryBuilder->getQuery();
        return $query->execute();
    }
}