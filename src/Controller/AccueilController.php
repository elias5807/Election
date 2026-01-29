<?php

namespace App\Controller;

use App\Entity\Respo;
use App\Form\MonPoleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse; // <--- 1. Import nécessaire pour l'AJAX
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AccueilController extends AbstractController
{
    #[Route('/responsable', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // A. Récupération du User et du Pole
        /** @var Respo $user */
        $user = $this->getUser();
        $pole = $user->getPole();

        if (!$pole) {
            return $this->render('accueil/index.html.twig', [
                'pole' => null,
                'monFormulaire' => null
            ]);
        }

        // B. Création du Formulaire
        $form = $this->createForm(MonPoleType::class, $pole);
        $form->handleRequest($request);

        // C. Traitement de la sauvegarde
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Sauvegarde en BDD

            // --- MODIFICATION ICI : Gestion de l'AJAX ---
            
            // 1. Si la requête vient du JavaScript (auto-save)
            if ($request->isXmlHttpRequest()) {
                // On renvoie du JSON pour dire "C'est bon" sans recharger la page
                return new JsonResponse(['status' => 'success', 'message' => 'Sauvegarde réussie']);
            }

            // 2. Si c'est une soumission classique (bouton valider ou submit() standard)
            $this->addFlash('success', 'Informations mises à jour !');
            return $this->redirectToRoute('app_home');
        }

        // D. Gestion des erreurs de validation en AJAX
        // Si le formulaire est soumis mais invalide (ex: champ vide requis), on renvoie une erreur au JS
        if ($form->isSubmitted() && !$form->isValid() && $request->isXmlHttpRequest()) {
            // On récupère les erreurs pour les voir dans la console si besoin
            return new JsonResponse(['status' => 'error', 'message' => 'Formulaire invalide'], 400);
        }

        // E. Envoi à la Vue (Affichage initial)
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'pole' => $pole,
            'monFormulaire' => $form->createView(),
        ]);
    }   
}