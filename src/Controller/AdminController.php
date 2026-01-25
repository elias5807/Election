<?php

namespace App\Controller;

use App\Repository\MilitantRepository;
use App\Repository\PoleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route(path: '/admin', name: 'app_admin')]
    public function index(PoleRepository $poleRepository, MilitantRepository $militantRepository): Response
    {
        // 1. Récupérer TOUS les militants actifs maintenant
        $tousMilitants = $militantRepository->militantDispo();

        // 2. Initialiser le tableau de tri
        // On commence par la colonne "Militants Disponibles" (ceux sans pôle)
        $militantsTries = [
            'Militants Disponibles' => [] 
        ];

        // On récupère la liste de tous les pôles pour créer les colonnes vides
        // Cela permet d'afficher "Pôle Logistique" même s'il n'y a personne dedans
        $tousLesPoles = $poleRepository->findAll();
        foreach ($tousLesPoles as $pole) {
            // Attention : Assurez-vous que la méthode getNomPole() existe dans l'entité Pole
            $militantsTries[$pole->getNomPole()] = [];
        }

        // 3. Algorithme de tri (Adapté pour ManyToMany)
        foreach ($tousMilitants as $militant) {
            
            // On récupère la collection des pôles du militant
            $lesPolesDuMilitant = $militant->getPoles();

            if ($lesPolesDuMilitant->isEmpty()) {
                // CAS A : Le militant n'est assigné à aucun pôle
                $militantsTries['Militants Disponibles'][] = $militant;
            } else {
                // CAS B : Le militant a un ou plusieurs pôles
                // On boucle sur ses pôles pour le mettre dans chaque colonne correspondante
                foreach ($lesPolesDuMilitant as $pole) {
                    $nomDuPole = $pole->getNomPole();
                    
                    // Sécurité : On vérifie que la colonne existe bien
                    if (isset($militantsTries[$nomDuPole])) {
                        $militantsTries[$nomDuPole][] = $militant;
                    }
                }
            }
        }

        // 4. Récupérer les stats globales (Total des voix/adhérents)
        $stats = $poleRepository->getGlobalStats();

        return $this->render('admin/index.html.twig', [
            'militantsTries' => $militantsTries,
            'stats' => $stats,
        ]);
    }
}