<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\PeopleAddressLastViewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\People;

#[ORM\Entity(repositoryClass: PeopleAddressLastViewRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
//            'normalization_context' => ['groups' => ['people_address_last_view:list']]
        ]
    ],
    itemOperations: [
        'get' => [
//            'normalization_context' => ['groups' => ['people_address_last_view:item']]
        ]
    ],
    order: ['date_start' => 'ASC'],
    paginationEnabled: false,
)]
#[ApiFilter(SearchFilter::class, properties: ['people' => 'exact'])]
class PeopleAddressLastView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    #[Groups(['people_address_last_view:list', 'people_address_last_view:item', 'people:item:read', 'people:item:write'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['people_address_last_view:list', 'people_address_last_view:item', 'people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $localityValue;

    #[ORM\Column(type: 'smallint')]
    #[Groups(['people_address_last_view:list', 'people_address_last_view:item', 'people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $localityType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['people_address_last_view:list', 'people_address_last_view:item', 'people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['people_address_last_view:list', 'people_address_last_view:item', 'people:item:read', 'people:item:write'])]
    private $note;

    #[ORM\ManyToOne(targetEntity: People::class, inversedBy: 'lastViewAddresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(type: People::class)]
    #[Groups(['people_address_last_view:list', 'people_address_last_view:item'])]
    private $people;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Groups(['people_address_last_view:list', 'people_address_last_view:item', 'people:item:read', 'people:item:write'])]
    private $dateStart;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Groups(['people_address_last_view:list', 'people_address_last_view:item', 'people:item:read', 'people:item:write'])]
    private $dateEnd;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocalityValue(): ?string
    {
        return $this->localityValue;
    }

    public function setLocalityValue(string $localityValue): self
    {
        $this->localityValue = $localityValue;

        return $this;
    }

    public function getLocalityType(): ?int
    {
        return $this->localityType;
    }

    public function setLocalityType(int $localityType): self
    {
        $this->localityType = $localityType;

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
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeImmutable $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeImmutable $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function __toString(): string
    {
        return $this->localityValue . ' ' . $this->address;
    }
}
