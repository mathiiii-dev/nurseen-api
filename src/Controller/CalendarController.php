<?php

namespace App\Controller;

use App\Handler\CalendarHandler;
use App\Manager\KidManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    private CalendarHandler $calendarHandler;
    private KidManager $kidManager;

    public function __construct(CalendarHandler $calendarHandler, KidManager $kidManager)
    {
        $this->calendarHandler = $calendarHandler;
        $this->kidManager = $kidManager;
    }

    #[Route('/calendar/kid/{kidId}', name: 'app_calendar_kid')]
    public function calendarKid(Request $request, int $kidId): JsonResponse
    {
        $kid = $this->kidManager->getKid($kidId);
        $this->calendarHandler->handleCalendarCreate($request, $kid);
        return $this->json([], Response::HTTP_CREATED);
    }
}
