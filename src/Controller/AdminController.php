<?php

namespace App\Controller;

use App\Repository\MilitantRepository;
use App\Repository\PoleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(PoleRepository $poleRepository, MilitantRepository $militantRepository): Response
    {
        // 1. Récupérer TOUS les militants actifs maintenant
        $tousMilitants = $militantRepository->militantDispo();

        $stats = $poleRepository->getGlobalStats();


        return $this->render('admin/index.html.twig', [
            'militants' => $tousMilitants,
            'stats' => $stats,
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
}