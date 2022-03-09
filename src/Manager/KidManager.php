<?php

namespace App\Manager;

use App\Entity\Kid;
use App\Repository\KidRepository;
use App\Repository\NurseRepository;

class KidManager
{
    private KidRepository $kidRepository;
    private NurseRepository $nurseRepository;

    public function __construct(KidRepository $kidRepository, NurseRepository $nurseRepository)
    {
        $this->kidRepository = $kidRepository;
        $this->nurseRepository = $nurseRepository;
    }

    public function getKid(int $kidId): Kid
    {
        $kid = $this->kidRepository->findOneBy(['id' => $kidId]);
        if (!$kid) {
            throw new \Exception(
                'No kid found',
                404
            );
        }

        return $kid;
    }

    public function getKidsByNurse(int $nurseId): array
    {
        $nurse = $this->nurseRepository->findOneBy(['nurse' => $nurseId]);
        $kids = $this->kidRepository->findKidsByNurseNonArchived($nurse->getId());
        if (!$kids) {
            throw new \Exception(
                'No kids found',
                404
            );
        }

        return $kids;
    }

}