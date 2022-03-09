<?php

namespace App\Entity;

use App\Repository\NurseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NurseRepository::class)]
class Nurse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $nurse;

    #[ORM\OneToMany(mappedBy: 'nurse', targetEntity: Kid::class, orphanRemoval: true)]
    private $kids;

    public function __construct()
    {
        $this->kids = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNurse(): ?User
    {
        return $this->nurse;
    }

    public function setNurse(User $nurse): self
    {
        $this->nurse = $nurse;

        return $this;
    }

    /**
     * @return Collection<int, Kid>
     */
    public function getKids(): Collection
    {
        return $this->kids;
    }

    public function addKid(Kid $kid): self
    {
        if (!$this->kids->contains($kid)) {
            $this->kids[] = $kid;
            $kid->setNurse($this);
        }

        return $this;
    }

    public function removeKid(Kid $kid): self
    {
        if ($this->kids->removeElement($kid)) {
            // set the owning side to null (unless already changed)
            if ($kid->getNurse() === $this) {
                $kid->setNurse(null);
            }
        }

        return $this;
    }
}
