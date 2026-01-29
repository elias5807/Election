<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PoleRepository;
use App\Repository\StandRepository;

final class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(PoleRepository $poleRepository, StandRepository $standRepository): Response
    {
        
        $localisations = $poleRepository->localisationPoles();
        $alertes = $standRepository->findBesoinsLogistiques();
        $stats = $poleRepository->getGlobalStats();

        return $this->render('map/index.html.twig', [
            'poles' => $localisations,
            'stats' => $stats,
            'alertes' => $alertes,
        ]);
    }
}
