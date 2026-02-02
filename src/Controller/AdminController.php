<?php

namespace App\Controller;

use App\Entity\Militant;
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
    #[Route('/admin', name: 'app_admin')]
    public function index(PoleRepository $poleRepo, MilitantRepository $militantRepo): Response
    {
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

        // Recherche QueryBuilder
        $militantsResults = $militantRepo->createQueryBuilder('m')
            ->leftJoin('m.pole', 'p')
            ->where('m.nom LIKE :t OR m.prenom LIKE :t OR m.mail LIKE :t')
            ->setParameter('t', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();

        $params = [
            'poles' => $poleRepo->findAllOrderedCustom(),
            'militantsParPole' => $this->grouperMilitants($militantsResults),
            'searchTerm' => $searchTerm,
        ];

        if ($request->isXmlHttpRequest()) {
            // On renvoie le fragment qui contient SIDEBAR + BOARD
            return $this->render('admin/_dashboard_content.html.twig', $params);
        }

        return $this->render('admin/index.html.twig', $params);
    }
    
    private function grouperMilitants(array $militants): array
    {
        $groupes = [];
        foreach ($militants as $m) {
            $poleId = $m->getPole() ? $m->getPole()->getId() : 'sans-pole';
            $groupes[$poleId][] = $m;
        }
        return $groupes;
    }

    #[Route('/militant/{id}/change-pole', name: 'app_militant_change_pole', methods: ['POST'])]
    public function changePole(Militant $militant, Request $request, PoleRepository $poleRepository, EntityManagerInterface $em): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);
        $nouveauNomPole = $data['poleNom'] ?? null;
        if (!$nouveauNomPole) return new JsonResponse(['status' => 'error'], 400);

        $nouveauPole = $poleRepository->findOneBy(['nomPole' => $nouveauNomPole]);
        if (!$nouveauPole) return new JsonResponse(['status' => 'error'], 404);

        $militant->setPole($nouveauPole);
        $em->flush();
        return new JsonResponse(['status' => 'success']);
    }

    #[Route('/admin/militant/{id}/toggle-repas', name: 'app_admin_toggle_repas', methods: ['POST'])]
    public function toggleRepas(Militant $militant, EntityManagerInterface $em): JsonResponse
    {
        $militant->setAMange(!$militant->isAMange());
        $em->flush();
        return new JsonResponse(['aMange' => $militant->isAMange()]);
    }  
}