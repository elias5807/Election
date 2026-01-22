<?php

namespace App\Repository;

use App\Entity\Pole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pole>
 */
class PoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pole::class);
    }

    /**
     * Calcule la somme totale de la colonne 'uni'
     * @return int
     */
    public function sumUni(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('SUM(p.uni)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Si vous avez besoin de sommer les autres colonnes (faep, ue, coordo)
    // Vous pouvez faire une seule requête pour tout récupérer d'un coup :
    public function getGlobalStats(): array
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.uni) as totalUni')
            ->addSelect('SUM(p.faep) as totalFaep')
            ->addSelect('SUM(p.ue) as totalUe')
            ->addSelect('SUM(p.unef) as totalUnef')
            ->getQuery()
            ->getSingleResult(); 
            // Retournera un tableau : ['totalUni' => 120, 'totalFaep' => 45, ...]
    }
}