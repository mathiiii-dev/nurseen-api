<?php

namespace App\Handler;

use App\Entity\Calendar;
use App\Entity\Kid;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CalendarHandler
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function handleCalendarCreate(Request $request, Kid $kid)
    {
        $data = $request->toArray();

        if(!$data['timeRanges'][0] or !$data['timeRanges'][1] or !$data['day']) {
            throw new BadRequestHttpException('Informations manquantes! Veuillez remplir tout les champs.');
        }

        $calendar = (new Calendar())
            ->setKid($kid)
            ->setArrival(new \DateTime($data['timeRanges'][0]))
            ->setDeparture(new \DateTime($data['timeRanges'][1]))
            ->setDay(new \DateTime($data['day']));

        $entityManager = $this->doctrine->getManager();

        $entityManager->persist($calendar);
        $entityManager->flush();
    }
}