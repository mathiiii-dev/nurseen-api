<?php

namespace App\Manager;

use App\Entity\Kid;
use App\Repository\FamilyRepository;
use App\Repository\KidRepository;
use App\Repository\NurseRepository;

class KidManager
{
    private KidRepository $kidRepository;
    private NurseRepository $nurseRepository;
    private FamilyRepository $familyRepository;

    public function __construct(KidRepository $kidRepository, NurseRepository $nurseRepository, FamilyRepository $familyRepository)
    {
        $this->kidRepository = $kidRepository;
        $this->nurseRepository = $nurseRepository;
        $this->familyRepository = $familyRepository;
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

        if (!$nurse) {
            throw new \Exception(
                'No nurse found',
                404
            );
        }

        $kids = $this->kidRepository->findKidsByNurseNonArchived($nurse->getId());

        if (!$kids) {
            throw new \Exception(
                'No kids found',
                404
            );
        }

        return $kids;
    }

    public function getKidsByFamily(int $familyId): array
    {
        $family = $this->familyRepository->findOneBy(['parent' => $familyId]);

        if (!$family) {
            throw new \Exception(
                'No family found',
                404
            );
        }

        $kids = $this->kidRepository->findBy(['family' => $family->getId()]);

        if (!$kids) {
            throw new \Exception(
                'No kids found',
                404
            );
        }

        return $kids;
    }

}