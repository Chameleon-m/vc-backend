<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\PeopleSearchFilter;
use App\Repository\PeopleRepository;
use App\Validator\IsValidOwner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Doctrine\PeopleSetOwnerListener;

#[ORM\EntityListeners([PeopleSetOwnerListener::class])]
#[ORM\Entity(repositoryClass: PeopleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('slug')]
#[ApiResource(
    collectionOperations: [
        'get' => [
//            'security' => "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
        ],
        'post' => [
            'security' => "is_granted('ROLE_USER')"
        ]
    ],
    itemOperations: [
        'get' => [
//            'security' => "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
        ],
        'put' => [
            'security' => "is_granted('PEOPLE_ITEM_EDIT', object)",
            'security_message' => 'Only the creator can edit a people'
        ],
        'delete' => [
            'security' => "is_granted('PEOPLE_ITEM_DELETE', object)"
        ],
        'patch' => [
            'security' => "is_granted('PEOPLE_ITEM_PATCH', object)"
        ]
    ],
    shortName: 'people',
    attributes: [
        'pagination_items_per_page' => 10,
        'formats' => ["jsonld", "json", "html", "csv" => ["text/csv"]]
    ],
    order: ['id' => 'DESC'],
    paginationEnabled: true
)]
#[ApiFilter(
    SearchFilter::class, properties: [
//        'firstName' => 'start',
        'firstName' => 'exact',
        'secondName' => 'exact',
        'middleName' => 'exact',
        'lastViewAddresses.address' => 'partial'
    ],
)]
#[ApiFilter(PeopleSearchFilter::class, arguments: ["useLike" => true])]
class People
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(
        min: 3,
        max: 255,
//        minMessage: '',
//        maxMessage: ''
    )]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $secondName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $middleName;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $birthdayDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $addressResidental;

    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $contacts;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeoplePhone::class, orphanRemoval: true)]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $phones;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeoplePhoto::class, orphanRemoval: true)]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $photos;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeopleAddressLastView::class, cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Valid]
    #[ApiSubresource]
    #[Groups(['people:collection:read', 'people:collection:write', 'people:item:read', 'people:item:write'])]
    private $lastViewAddresses;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['people:read', 'admin:write'])]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['people:read', 'admin:write'])]
    private $slug;

    #[ORM\Column(type: 'string', length: 255, options: ["default" => "submitted"])]
    #[Groups(['people:read', 'admin:write'])]
    private $state = 'submitted';

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'people')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['people:read', 'people:collection:post', 'admin:write'])]
    #[IsValidOwner]
    private $owner;

    public function __construct()
    {
        $this->phones = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->lastViewAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->secondName;
    }

    public function setSecondName(string $secondName): self
    {
        $this->secondName = $secondName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getAddressResidental(): ?string
    {
        return $this->addressResidental;
    }

    public function setAddressResidental(?string $addressResidental): self
    {
        $this->addressResidental = $addressResidental;

        return $this;
    }

    public function getContacts(): array
    {
        return $this->contacts;
    }

    public function setContacts(array $contacts): self
    {
        $this->contacts = $contacts;

        return $this;
    }

    public function getBirthdayDate(): ?\DateTimeImmutable
    {
        return $this->birthdayDate;
    }

    public function setBirthdayDate(?\DateTimeImmutable $birthdayDate): self
    {
        $this->birthdayDate = $birthdayDate;

        return $this;
    }

    /**
     * @return Collection<int, PeoplePhone>
     */
    public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function addPhone(PeoplePhone $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones[] = $phone;
            $phone->setPeople($this);
        }

        return $this;
    }

    public function removePhone(PeoplePhone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            // set the owning side to null (unless already changed)
            if ($phone->getPeople() === $this) {
                $phone->setPeople(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PeoplePhoto>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(PeoplePhoto $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setPeople($this);
        }

        return $this;
    }

    public function removePhoto(PeoplePhoto $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getPeople() === $this) {
                $photo->setPeople(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PeopleAddressLastView>
     */
    public function getLastViewAddresses(): Collection
    {
        return $this->lastViewAddresses;
    }

    public function addLastViewAddress(PeopleAddressLastView $lastViewAddress): self
    {
        if (!$this->lastViewAddresses->contains($lastViewAddress)) {
            $this->lastViewAddresses[] = $lastViewAddress;
            $lastViewAddress->setPeople($this);
        }

        return $this;
    }

    public function removeLastViewAddress(PeopleAddressLastView $lastViewAddress): self
    {
        if ($this->lastViewAddresses->removeElement($lastViewAddress)) {
            // set the owning side to null (unless already changed)
            if ($lastViewAddress->getPeople() === $this) {
                $lastViewAddress->setPeople(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        //TODO: unique
        return $this->firstName . ' ' . $this->secondName . ' ' . $this->middleName . ' ' . random_int(1, 10000);
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function computeSlug(SluggerInterface $slugger)
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string)$slugger->slug((string)$this)->lower();
        }
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
