<?php

namespace App\Handler;

use App\Entity\Family;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class FamilyHandler
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function handleFamilyCreate(User $user)
    {
        $entityManager = $this->doctrine->getManager();

        $nurse = new Family();
        $nurse->setParent($user);

        $entityManager->persist($nurse);

        $entityManager->flush();
    }
}