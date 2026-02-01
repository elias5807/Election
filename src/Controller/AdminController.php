<?php

namespace App\Controller;

use App\Entity\Militant;
use App\Entity\Pole;
use App\Repository\PoleRepository;
use App\Repository\MilitantRepository;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // src/Controller/AdminController.php
    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request, PoleRepository $poleRepository, MilitantRepository $militantRepository): Response
    {
        $query = $request->query->get('q');
        $allMilitants = $militantRepository->findBySearch($query);
        dd($allMilitants); // Ceci va arrêter le code et afficher les résultats.
        // Récupération du terme de recherche depuis l'URL (ex: /admin?q=durand)
        $searchTerm = $request->query->get('q');

        // 1. Statistiques globales (restent inchangées)
        $nbFaep = $militantRepository->countFaep();
        $stats = $poleRepository->getGlobalStats();
        $poles = $poleRepository->findAll();

        // 2. Récupération filtrée ou globale
        if ($searchTerm) {
            $allMilitants = $militantRepository->findBySearchTerm($searchTerm);
        } else {
            $allMilitants = $militantRepository->findAllMilitantsPourDashboard();
        }

        // 3. Groupement (ton code actuel fonctionne toujours)
        $militantsGroupes = [];
        foreach ($allMilitants as $m) {
            $poleId = $m->getPole() ? $m->getPole()->getId() : 'sans-pole';
            $militantsGroupes[$poleId][] = $m;
        }

        return $this->render('admin/index.html.twig', [
            'militantsParPole' => $militantsGroupes,
            'nbFaep' => $nbFaep,
            'stats' => $stats,
            'poles' => $poles,
            'searchTerm' => $searchTerm // On renvoie le terme pour l'afficher dans l'input
        ]);
    }
    /**
     * Route utilisée par le Drag & Drop pour mettre à jour le pôle
     */
    #[Route('/militant/{id}/change-pole', name: 'app_militant_change_pole', methods: ['POST'])]
    public function changePole(
        Militant $militant, 
        Request $request, 
        PoleRepository $poleRepository, 
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $nouveauNomPole = $data['poleNom'] ?? null;

        if (!$nouveauNomPole) {
            return new JsonResponse(['status' => 'error', 'message' => 'Nom du pôle manquant'], 400);
        }

        // On cherche par 'nomPole' (le nom de la propriété dans ton entité Pole)
        $nouveauPole = $poleRepository->findOneBy(['nomPole' => $nouveauNomPole]);

        if (!$nouveauPole) {
            return new JsonResponse(['status' => 'error', 'message' => 'Pôle introuvable'], 404);
        }

        $militant->setPole($nouveauPole);
        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * Route pour cocher/décocher le repas en AJAX
     */
    #[Route('/admin/militant/{id}/toggle-repas', name: 'app_admin_toggle_repas', methods: ['POST'])]
    public function toggleRepas(Militant $militant, EntityManagerInterface $em): JsonResponse
    {
        // On inverse l'état actuel
        $militant->setAMange(!$militant->isAMange());
        $em->flush();
        
        return new JsonResponse(['aMange' => $militant->isAMange()]);
    }  
}