<?php

namespace App\Controller;

use App\Entity\Militant;
use App\Entity\Pole;
use App\Repository\PoleRepository;
use App\Repository\MilitantRepository;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // src/Controller/AdminController.php
    #[Route('/admin', name: 'app_admin')]
    public function index(PoleRepository $poleRepo, MilitantRepository $militantRepo): Response
    {
        // Affichage normal (tous les militants)
        $allMilitants = $militantRepo->findAllMilitantsPourDashboard();
        
        return $this->render('admin/index.html.twig', [
            'militantsParPole' => $this->grouperMilitants($allMilitants),
            'poles'            => $poleRepo->findAllOrderedCustom(),
            'searchTerm'       => null,
            'nbFaep'           => $militantRepo->countFaep(),
            'stats'            => $poleRepo->getGlobalStats(),
        ]);
    }

    #[Route('/admin/search', name: 'app_admin_search', methods: ['GET'])]
    public function search(Request $request, PoleRepository $poleRepo, MilitantRepository $militantRepo): Response
    {
        $searchTerm = trim($request->query->get('q', ''));

        $militantsResults = $militantRepo->createQueryBuilder('m')
            ->leftJoin('m.pole', 'p')
            ->addSelect('p')
            ->where('m.nom LIKE :t OR m.prenom LIKE :t OR m.mail LIKE :t OR m.tel LIKE :t')
            ->setParameter('t', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();

        $params = [
            'poles'      => $poleRepo->findAllOrderedCustom(),
            'militants'  => $militantsResults,
            'searchTerm' => $searchTerm,
            'stats'      => $poleRepo->getGlobalStats(),
        ];

        // Si c'est une requête AJAX (Stimulus), on ne rend que le board
        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/_board.html.twig', $params);
        }

        // Sinon, on rend la page complète (cas où on appuie sur Entrée)
        return $this->render('admin/index.html.twig', $params);
    }

    /**
     * Petite fonction utilitaire pour éviter de répéter le foreach du groupement
     */
    private function grouperMilitants(array $militants): array
    {
        $groupes = [];
        foreach ($militants as $m) {
            $poleId = $m->getPole() ? $m->getPole()->getId() : 'sans-pole';
            $groupes[$poleId][] = $m;
        }
        return $groupes;
    }
    
    /**
     * Route utilisée par le Drag & Drop pour mettre à jour le pôle
     */
    #[Route('/militant/{id}/change-pole', name: 'app_militant_change_pole', methods: ['POST'])]
    public function changePole(
        Militant $militant, 
        Request $request, 
        PoleRepository $poleRepository, 
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $nouveauNomPole = $data['poleNom'] ?? null;

        if (!$nouveauNomPole) {
            return new JsonResponse(['status' => 'error', 'message' => 'Nom du pôle manquant'], 400);
        }

        // On cherche par 'nomPole' (le nom de la propriété dans ton entité Pole)
        $nouveauPole = $poleRepository->findOneBy(['nomPole' => $nouveauNomPole]);

        if (!$nouveauPole) {
            return new JsonResponse(['status' => 'error', 'message' => 'Pôle introuvable'], 404);
        }

        $militant->setPole($nouveauPole);
        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * Route pour cocher/décocher le repas en AJAX
     */
    #[Route('/admin/militant/{id}/toggle-repas', name: 'app_admin_toggle_repas', methods: ['POST'])]
    public function toggleRepas(Militant $militant, EntityManagerInterface $em): JsonResponse
    {
        // On inverse l'état actuel
        $militant->setAMange(!$militant->isAMange());
        $em->flush();
        
        return new JsonResponse(['aMange' => $militant->isAMange()]);
    }  
}