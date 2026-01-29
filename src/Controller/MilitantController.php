<?php

namespace App\Controller;

use App\Entity\Militant;
use App\Entity\Respo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

final class MilitantController extends AbstractController
{
    #[Route('/militant', name: 'app_militant')]
    #[IsGranted('ROLE_USER')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var Respo $user */
        $user = $this->getUser();
        $pole = $user->getPole();

        // Sécurité : Si le respo n'a pas de pôle, liste vide
        if (!$pole) {
            return $this->render('militant/index.html.twig', [
                'militants' => []
            ]);
        }

        // --- LA REQUÊTE EST ICI ---
        // On demande au Repository : "Trouve-moi tous les militants où 'pole' = le pôle du user"
        $militants = $em->getRepository(Militant::class)->findBy(
            ['pole' => $pole],  // Critère (WHERE id_pole = X)
            ['nom' => 'ASC']    // Tri optionnel (par ordre alphabétique)
        );

        return $this->render('militant/index.html.twig', [
            'militants' => $militants,
            'pole' => $pole
        ]);
    }

    #[Route('/militant/toggle-repas/{id}', name: 'app_militant_toggle_repas', methods: ['POST'])]
    public function toggleRepas(Militant $militant, EntityManagerInterface $em): JsonResponse
    {
        try {
            // 1. On inverse
            $militant->setAMange(!$militant->isAMange());
            
            // 2. On sauvegarde
            $em->flush();

            return new JsonResponse([
                'status' => 'success', 
                'nouvelEtat' => $militant->isAMange()
            ]);
        } catch (\Exception $e) {
            // C'EST ICI QUE LA MAGIE OPÈRE : On renvoie l'erreur précise
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
