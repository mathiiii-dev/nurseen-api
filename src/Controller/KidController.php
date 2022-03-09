<?php

namespace App\Controller;

use App\Manager\KidManager;
use App\Repository\KidRepository;
use App\Repository\NurseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KidController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private KidManager $kidManager;

    public function __construct(KidManager $kidManager, ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->kidManager = $kidManager;
    }

    #[Route('/kid/nurse/{nurseId}', name: 'app_kid_nurse')]
    public function index(int $nurseId): JsonResponse
    {
        $kids = $this->kidManager->getKidsByNurse($nurseId);

        return $this->json($kids, Response::HTTP_OK, [], ['groups' => 'kid_list']);

    }

    #[Route('/kid/{kidId}/activate', name: 'app_kid_nurse_activate', methods: 'POST')]
    public function activate(int $kidId): JsonResponse
    {
        $kid = $this->kidManager->getKid($kidId);
        $activated = $kid->getActivated();
        $kid->setActivated(!$activated);
        $entityManager = $this->doctrine->getManager();
        $entityManager->flush();

        return $this->json($kid, Response::HTTP_OK, [], ['groups' => 'kid_list']);
    }

    #[Route('/kid/{kidId}/archive', name: 'app_kid_nurse_achive', methods: 'POST')]
    public function archive(int $kidId): JsonResponse
    {
        $kid = $this->kidManager->getKid($kidId);

        $kid->setArchived(true);
        $entityManager = $this->doctrine->getManager();
        $entityManager->flush();

        return $this->json($kid, Response::HTTP_OK, [], ['groups' => 'kid_list']);
    }
}
