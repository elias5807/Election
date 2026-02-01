<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PoleRepository;
use App\Repository\StandRepository;
use App\Repository\MilitantRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(PoleRepository $poleRepository, StandRepository $standRepository, MilitantRepository $militantRepository): Response
    {
        
        $localisations = $poleRepository->localisationPoles();
        $alertes = $standRepository->findBesoinsLogistiques();
        $stats = $poleRepository->getGlobalStats();
        $nbFaep = $militantRepository->countFaep();

        return $this->render('map/index.html.twig', [
            'poles' => $localisations,
            'stats' => $stats,
            'alertes' => $alertes,
            'nbFaep' => $nbFaep,
        ]);
    }
}
