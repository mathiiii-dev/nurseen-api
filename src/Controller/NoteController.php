<?php

namespace App\Controller;

use App\Handler\NoteHandler;
use App\Manager\KidManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{

    private NoteHandler $noteHandler;
    private KidManager $kidManager;

    public function __construct(NoteHandler $noteHandler, KidManager $kidManager)
    {
        $this->noteHandler = $noteHandler;
        $this->kidManager = $kidManager;
    }

    #[IsGranted('ROLE_NURSE', message: 'Vous ne pouvez pas faire ça')]
    #[Route('/note/kid/{kidId}', name: 'app_note', methods: 'POST')]
    public function create(Request $request, int $kidId): Response
    {
        $kid = $this->kidManager->getKid($kidId);
        $this->denyAccessUnlessGranted('owner', $kid);
        $this->noteHandler->handleCreateNote($request, $kid);
        return $this->json([], Response::HTTP_CREATED);
    }
}
