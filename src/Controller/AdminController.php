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
}