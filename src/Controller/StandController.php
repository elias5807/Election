<?php

namespace App\Controller;

use App\Entity\Respo;
use App\Entity\Stand;
use App\Entity\Pole;
use App\Form\MonStandType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class StandController extends AbstractController
{
    #[Route('/stand', name: 'app_stand')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Respo $user */
        $user = $this->getUser();
        
        // 1. On récupère d'abord le Pôle du responsable
        $pole = $user->getPole();

        // Sécurité : Si le user n'a pas de pôle, il ne peut pas avoir de stand
        if (!$pole) {
            return $this->render('stand/index.html.twig', [
                'form' => null,
                'error' => "Vous n'êtes affecté à aucun pôle."
            ]);
        }

        // 2. On récupère le Stand VIA le Pôle
        // C'est ici que la magie opère : Respo -> Pole -> Stand
        $stand = $pole->getStand();

        if (!$stand) {
            return $this->render('stand/index.html.twig', [
                'form' => null,
                'error' => "Votre pôle n'a pas de stand assigné."
            ]);
        }

        // 3. Création du formulaire avec le stand trouvé
        $form = $this->createForm(MonStandType::class, $stand);
        $form->handleRequest($request);

        // --- SAUVEGARDE ---
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['status' => 'success']);
            }
            
            // Flash message pour la version sans JS
            $this->addFlash('success', 'Stocks mis à jour !');
            return $this->redirectToRoute('app_stand');
        }

        // Erreurs AJAX
        if ($form->isSubmitted() && !$form->isValid() && $request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => 'error'], 400);
        }

        return $this->render('stand/index.html.twig', [
            'form' => $form->createView(),
            'error' => null // Pas d'erreur
        ]);
    }
}