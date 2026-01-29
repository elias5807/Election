<?php

namespace App\Controller;

use App\Entity\Militant;              // Pour l'entité Militant
use App\Entity\Pole;                  // (Optionnel selon ton code, mais utile)
use App\Repository\PoleRepository;    // Pour trouver le pôle
use App\Repository\MilitantRepository; // Pour trouver les militants
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface; // Pour sauvegarder
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(PoleRepository $poleRepository, MilitantRepository $militantRepository): Response
    {
        // 1. Récupérer TOUS les militants actifs maintenant
        $tousMilitants = $militantRepository->militantDispo();

        $stats = $poleRepository->getGlobalStats();
        $poles = $poleRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'militants' => $tousMilitants,
            'stats' => $stats,
            'poles' => $poles,
        ]);
    }

    #[Route('/militant/update-pole', name: 'militant_update_pole', methods: ['POST'])]
    public function updatePole(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $militantId = $data['id'] ?? null;
        $poleName = $data['pole'] ?? null;

        $militant = $em->getRepository(Militant::class)->find($militantId);

        if (!$militant) {
            return new JsonResponse(['status' => 'error', 'message' => 'Militant non trouvé'], 404);
        }

        // Gestion du cas "Non assigné" (si poleName est 'null' ou vide)
        if ($poleName === 'null' || empty($poleName)) {
            $militant->setPole(null);
        } else {
            // Trouver le pôle par son nom
            $pole = $em->getRepository(Pole::class)->findOneBy(['nomPole' => $poleName]);
            if ($pole) {
                $militant->setPole($pole);
            }
        }

        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }
    
    /**
     * C'est cette route qui est appelée par le JavaScript 'fetch'
     */
    #[Route('/militant/{id}/change-pole', name: 'app_militant_change_pole', methods: ['POST'])]
    public function changePole(
        Militant $militant, 
        Request $request, 
        PoleRepository $poleRepository, 
        EntityManagerInterface $em
    ): JsonResponse
    {
        // 1. On récupère les données envoyées par le JS (le JSON)
        $data = json_decode($request->getContent(), true);
        $nouveauNomPole = $data['poleNom'] ?? null;

        if (!$nouveauNomPole) {
            return new JsonResponse(['status' => 'error', 'message' => 'Nom du pôle manquant'], 400);
        }

        // 2. On cherche l'entité du nouveau Pôle en base de données
        $nouveauPole = $poleRepository->findOneBy(['nomPole' => $nouveauNomPole]);

        if (!$nouveauPole) {
            return new JsonResponse(['status' => 'error', 'message' => 'Pôle introuvable'], 404);
        }

        // 3. On met à jour le militant
        $militant->setPole($nouveauPole);

        // 4. On sauvegarde (C'est ici que la BDD est modifiée !)
        $em->flush();

        // 5. On répond au JS que tout s'est bien passé
        return new JsonResponse(['status' => 'success']);
    }

    // src/Controller/AdminController.php

    #[Route('/admin/militant/{id}/toggle-repas', name: 'app_admin_toggle_repas', methods: ['POST'])]
    public function toggleRepas(Militant $militant, EntityManagerInterface $em): JsonResponse
    {
        $militant->setAMange(!$militant->isAMange());
        $em->flush();
        return new JsonResponse(['aMange' => $militant->isAMange()]);
    }  
}