<?php

namespace App\Repository;

use App\Entity\Stand;
use App\Entity\Pole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stand>
 *
 * @method Stand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stand[]    findAll()
 * @method Stand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stand::class);
    }

    // Exemple : Trouver les stands qui ont besoin de crêpes
    /*
    public function findStandsAvecCrepes(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.crepe = :val')
            ->setParameter('val', true)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findBesoinsLogistiques(): array
    {
        // 1. On récupère tous les stands AVEC leur pôle associé (pour avoir le nom)
        $stands = $this->createQueryBuilder('s')
            ->leftJoin('s.pole', 'p')
            ->addSelect('p') // Optimisation : évite une requête SQL supplémentaire par stand
            ->getQuery()
            ->getResult();

        $resultat = [];

        // 2. On boucle sur chaque stand pour vérifier les stocks
        foreach ($stands as $stand) {
            $manquants = [];

            // On vérifie chaque ingrédient. Si c'est FALSE (ou null), on l'ajoute à la liste.
            if (!$stand->isCrepe())  $manquants[] = 'Pâte à crêpe';
            if (!$stand->isLait())   $manquants[] = 'Lait';
            if (!$stand->isOeuf())   $manquants[] = 'Oeufs';
            if (!$stand->isRhum())   $manquants[] = 'Rhum';
            if (!$stand->isFarine()) $manquants[] = 'Farine';

            // Si la liste des manquants n'est pas vide, on ajoute le stand au rapport
            if (!empty($manquants)) {
                // On récupère le nom du pôle, ou "Inconnu" si pas de pôle lié
                $nomStand = $stand->getPole() ? $stand->getPole()->getNomPole() : 'Stand sans pôle';

                $resultat[] = [
                    'nom_pole' => $nomStand,
                    'manquants' => $manquants, // C'est un tableau de chaînes de caractères
                    // Optionnel : coordonnées pour afficher sur la carte
                    'lat' => $stand->getPole() ? $stand->getPole()->getLattitude() : null,
                    'lng' => $stand->getPole() ? $stand->getPole()->getLongitude() : null,
                ];
            }
        }

        return $resultat;
    }
}