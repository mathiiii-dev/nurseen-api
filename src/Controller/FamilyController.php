<?php

namespace App\Controller;

use App\Handler\FamilyHandler;
use App\Handler\LinkCodeHandler;
use App\Repository\FamilyRepository;
use App\Service\LinkCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class FamilyController extends AbstractController
{
    private LinkCodeService $codeService;
    private FamilyHandler $familyHandler;
    private FamilyRepository $familyRepository;

    public function __construct(LinkCodeService $codeService, FamilyHandler $familyHandler, FamilyRepository $familyRepository)
    {
        $this->codeService = $codeService;
        $this->familyHandler = $familyHandler;
        $this->familyRepository = $familyRepository;
    }

    #[Route('/family/{familyId}/kid', name: 'app_family_kid', methods: 'POST')]
    public function link(Request $request, int $familyId): Response
    {
        try {
            $data = $request->toArray();
            $nurse = $this->codeService->validate($data['code']);
            $family = $this->familyRepository->findOneBy(['parent' => $familyId]);
            $this->familyHandler->handleFamilyKidCreate($data, $nurse, $family);
            return new JsonResponse([], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
