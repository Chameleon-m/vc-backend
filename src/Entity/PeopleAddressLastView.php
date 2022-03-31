<?php

namespace App\Entity;

use App\Repository\PeopleAddressLastViewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeopleAddressLastViewRepository::class)]
class PeopleAddressLastView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $locality_value;

    #[ORM\Column(type: 'smallint')]
    private $locality_type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $note;

    #[ORM\ManyToOne(targetEntity: People::class, inversedBy: 'last_view_addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private $people;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private $date_start;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private $date_end;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocalityValue(): ?string
    {
        return $this->locality_value;
    }

    public function setLocalityValue(string $locality_value): self
    {
        $this->locality_value = $locality_value;

        return $this;
    }

    public function getLocalityType(): ?int
    {
        return $this->locality_type;
    }

    public function setLocalityType(int $locality_type): self
    {
        $this->locality_type = $locality_type;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPeople(): ?People
    {
        return $this->people;
    }

    public function setPeople(?People $people): self
    {
        $this->people = $people;

        return $this;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->date_start;
    }

    public function setDateStart(?\DateTimeImmutable $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->date_end;
    }

    public function setDateEnd(?\DateTimeImmutable $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }
}
