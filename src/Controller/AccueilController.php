<?php

namespace App\Controller;

use App\Entity\Respo;
use App\Form\MonPoleType; // <--- 1. Importez votre formulaire
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // <--- 2. Importez Request
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AccueilController extends AbstractController
{
    #[Route('/responsable', name: 'app_home')]
    #[IsGranted('ROLE_USER')] // Sécurité
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // A. Récupération du User et du Pole
        /** @var Respo $user */
        $user = $this->getUser();
        $pole = $user->getPole();

        if (!$pole) {
            // Gestion d'erreur si pas de pôle
            return $this->render('accueil/index.html.twig', [
                'pole' => null,
                'monFormulaire' => null // On envoie null pour éviter l'erreur Twig
            ]);
        }

        // B. Création du Formulaire
        $form = $this->createForm(MonPoleType::class, $pole);
        $form->handleRequest($request);

        // C. Traitement de la sauvegarde
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Sauvegarde en BDD
            $this->addFlash('success', 'Informations mises à jour !');
            
            return $this->redirectToRoute('app_home'); // On recharge la page
        }

        // D. Envoi à la Vue
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'pole' => $pole,
            'monFormulaire' => $form->createView(), 
        ]);
    }   
}