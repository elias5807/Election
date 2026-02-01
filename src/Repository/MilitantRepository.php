<?php

namespace App\Repository;

use App\Entity\Militant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Militant>
 */
class MilitantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Militant::class);
    }

    /**
     * Récupère tous les militants pour le Dashboard (Trello-style)
     * Optimisé pour éviter les erreurs 500 et les ralentissements (N+1 queries)
     */
    public function findAllMilitantsPourDashboard(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.pole', 'p') // Jointure sur la relation 'pole' de l'entité Militant
            ->addSelect('p')           // Récupère les données du pôle en une seule requête
            ->orderBy('p.nomPole', 'ASC') // Utilisation du nom de la PROPRIÉTÉ PHP (nomPole)
            ->addOrderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre total de militants (utilisé pour les stats du dashboard)
     */
    public function countFaep(): int 
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)') // Utilise l'ID tel que défini dans ton entité Militant
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupère les militants disponibles avec leurs horaires (si besoin de filtrer par heure)
     */
    public function militantDispo(): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.horaires', 'h')
            ->addSelect('h') 
            ->leftJoin('m.pole', 'p')
            ->addSelect('p')
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}