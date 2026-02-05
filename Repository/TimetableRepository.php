<?php

namespace Disjfa\TimetableBundle\Repository;

use Disjfa\TimetableBundle\Entity\Timetable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class TimetableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timetable::class);
    }

    public function findAllByOptions(?UserInterface $user = null)
    {
        $qb = $this->createQueryBuilder('timetable');

        if ($user instanceof UserInterface) {
            $qb->join('timetable.users', 'users');
            $qb->andWhere('users.id = :user');
            $qb->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }
}
