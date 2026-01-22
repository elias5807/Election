<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PoleRepository;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(PoleRepository $poleRepository): Response
    {

        $stats = $poleRepository->getGlobalStats();
        $uni = $stats['totalUni'] ?? 0;
        $faep = $stats['totalFaep'] ?? 0;
        $ue = $stats['totalUe'] ?? 0;
        $unef = $stats['totalUnef'] ?? 0;

        return $this->render('admin/index.html.twig', [
            'faep' => $faep,
            'uni' => $uni, // Contient maintenant la vraie somme de la BDD
            'ue' => $ue,
            'unef' => $unef,
        ]);

    }

}
