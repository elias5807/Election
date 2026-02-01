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
     * Récupère les militants qui ont un créneau horaire actif maintenant.
     * @return Militant[]
     */
    public function militantDispo(): array
    {

        return $this->createQueryBuilder('m')
            // 1. Jointure OBLIGATOIRE sur les horaires pour vérifier l'heure
            ->innerJoin('m.horaires', 'h')
            
            // 2. OPTIMISATION (Eager Loading) : 
            // On sélectionne ("addSelect") les horaires et les pôles directement.
            // Cela évite que Symfony refasse 50 requêtes SQL quand vous ferez la boucle dans le HTML.
            ->addSelect('h') 
            ->leftJoin('m.pole', 'p')
            ->addSelect('p')
            // 3. Distinction pour éviter les doublons si jointures multiples
            ->distinct()

            ->getQuery()
            ->getResult();
    }

    public function findAllMilitantsPourDashboard(): array
    {
        try {
            return $this->createQueryBuilder('m')
                ->leftJoin('m.pole', 'p') // Assure-toi que 'pole' est bien le nom de la relation dans l'entité Militant
                ->addSelect('p')
                ->orderBy('p.nom', 'ASC')
                ->addOrderBy('m.nom', 'ASC')
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            // Cela te permettra de voir l'erreur réelle si tu as un debugger
            throw $e; 
        }
    }

    public function countFaep(): int {
        $now = new \DateTime();

        return (int) $this->createQueryBuilder('m')
            // On compte les identifiants uniques de 'm' (militant)
            ->select('COUNT(DISTINCT m.id)')

            ->getQuery()
            // On récupère une valeur unique (le nombre)
            ->getSingleScalarResult();
    }
}