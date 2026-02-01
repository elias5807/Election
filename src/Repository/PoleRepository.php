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

    public function localisationPoles(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.nomPole, p.lattitude, p.longitude, p.unef, p.ue, p.uni,p.tract,p.afluence')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les pôles dans l'ordre spécifique défini par le client
     * @return Pole[]
     */
    public function findAllOrderedCustom(): array
    {
        $poles = $this->findAll();

        $ordreVoulu = [
            'Campus Cathédrale', 
            'Campus Sud', 
            'Campus Science', 
            'Campus Citadelle', 
            'Campus Staps', 
            'Campus IUT', 
            'Campus Art', 
            'Campus IFMK', 
            'Campus Apradis', 
            'AGORAE', 
            'Deloc'
        ];

        usort($poles, function ($a, $b) use ($ordreVoulu) {
            $posA = array_search($a->getNomPole(), $ordreVoulu);
            $posB = array_search($b->getNomPole(), $ordreVoulu);

            // Si un pôle n'est pas dans la liste, on le met à la fin
            $posA = ($posA === false) ? 999 : $posA;
            $posB = ($posB === false) ? 999 : $posB;

            return $posA <=> $posB;
        });

        return $poles;
    }
}