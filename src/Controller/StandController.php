<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StandController extends AbstractController
{
    #[Route('/stand', name: 'app_stand')]
    public function index(): Response
    {
        return $this->render('stand/index.html.twig', [
            'controller_name' => 'StandController',
        ]);
    }
}
