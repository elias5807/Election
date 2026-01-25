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
        $now = new \DateTime();

        return $this->createQueryBuilder('m')
            // 1. Jointure OBLIGATOIRE sur les horaires pour vérifier l'heure
            ->innerJoin('m.horaires', 'h')
            
            // 2. OPTIMISATION (Eager Loading) : 
            // On sélectionne ("addSelect") les horaires et les pôles directement.
            // Cela évite que Symfony refasse 50 requêtes SQL quand vous ferez la boucle dans le HTML.
            ->addSelect('h') 
            ->leftJoin('m.poles', 'p') 
            ->addSelect('p')

            // 3. Conditions temporelles
            ->where('h.debut <= :now')
            ->andWhere('h.fin >= :now')
            ->setParameter('now', $now)

            // 4. Distinction pour éviter les doublons si jointures multiples
            ->distinct()

            ->getQuery()
            ->getResult();
    }
}