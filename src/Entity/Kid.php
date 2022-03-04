<?php

namespace App\Entity;

use App\Repository\KidRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KidRepository::class)]
class Kid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 50)]
    private $lastname;

    #[ORM\Column(type: 'date', nullable: true)]
    private $birthday;

    #[ORM\ManyToOne(targetEntity: Family::class, inversedBy: 'kids')]
    #[ORM\JoinColumn(nullable: false)]
    private $family;

    #[ORM\ManyToOne(targetEntity: Nurse::class, inversedBy: 'kids')]
    #[ORM\JoinColumn(nullable: false)]
    private $nurse;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getFamily(): ?Family
    {
        return $this->family;
    }

    public function setFamily(?Family $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getNurse(): ?Nurse
    {
        return $this->nurse;
    }

    public function setNurse(?Nurse $nurse): self
    {
        $this->nurse = $nurse;

        return $this;
    }
}
