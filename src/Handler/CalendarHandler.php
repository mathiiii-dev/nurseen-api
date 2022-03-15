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

        $date = (new \DateTime($data['day']))->setTimezone(new \DateTimeZone('Europe/Paris'));
        $arrival = (new \DateTime($data['timeRanges'][0]))->setTimezone(new \DateTimeZone('Europe/Paris'));
        $departure = (new \DateTime($data['timeRanges'][1]))->setTimezone(new \DateTimeZone('Europe/Paris'));

        $calendar = (new Calendar())
            ->setKid($kid)
            ->setArrival($arrival)
            ->setDeparture($departure)
            ->setDay($date);

        $entityManager = $this->doctrine->getManager();

        $entityManager->persist($calendar);
        $entityManager->flush();
    }
}