<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    // Exemple : Pour trouver un rôle par son nom technique (ROLE_ADMIN)
    public function findByRoleString(string $roleString): ?Role
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.roleString = :val')
            ->setParameter('val', $roleString)
            ->getQuery()
            ->getOneOrNullResult();
    }
}