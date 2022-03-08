<?php

namespace App\Controller;

use App\Repository\KidRepository;
use App\Repository\NurseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KidController extends AbstractController
{
    private KidRepository $kidRepository;
    private NurseRepository $nurseRepository;

    public function __construct(KidRepository $kidRepository, NurseRepository $nurseRepository)
    {
        $this->kidRepository = $kidRepository;
        $this->nurseRepository = $nurseRepository;
    }

    #[Route('/kid/nurse/{nurseId}', name: 'app_kid_nurse')]
    public function index(int $nurseId)
    {
        $nurse = $this->nurseRepository->findOneBy(['nurse' => $nurseId]);
        $kids = $this->kidRepository->findBy(['nurse' => $nurse->getId()]);
        if (!$kids) {
            throw new \Exception(
                'No kids found',
                404
            );
        }

        return $this->json($kids, Response::HTTP_OK, [], ['groups' => 'kid_list']);

    }
}
