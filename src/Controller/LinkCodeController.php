<?php

namespace App\Controller;

use App\Handler\LinkCodeHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkCodeController extends AbstractController
{
    private LinkCodeHandler $codeHandler;

    public function __construct(LinkCodeHandler $codeHandler)
    {
        $this->codeHandler = $codeHandler;
    }

    #[Route('/link_code/{nurse}', name: 'app_link_code', methods: 'POST')]
    public function index(Request $request, int $nurse): JsonResponse
    {
        try {
            $this->codeHandler->handleLinkeCodeCreate($request, $nurse);
            return new JsonResponse([], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
