<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PeopleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PeopleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('slug')]
#[ApiResource(
    collectionOperations: ['get' => ['normalization_context' => ['groups' => 'people:list']]],
    itemOperations: ['get' => ['normalization_context' => ['groups' => 'people:item']]],
    order: ['id' => 'DESC'],
    paginationEnabled: false,
)]
class People
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['people:list', 'people:item'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['people:list', 'people:item'])]
    private $first_name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['people:list', 'people:item'])]
    private $second_name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['people:list', 'people:item'])]
    private $middle_name;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Groups(['people:list', 'people:item'])]
    private $birthday_date;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['people:list', 'people:item'])]
    private $address_residental;

    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank]
    #[Groups(['people:list', 'people:item'])]
    private $contacts;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeoplePhone::class, orphanRemoval: true)]
    #[Groups(['people:list', 'people:item'])]
    private $phones;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeoplePhoto::class, orphanRemoval: true)]
    #[Groups(['people:list', 'people:item'])]
    private $photos;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeopleAddressLastView::class, orphanRemoval: true)]
    #[Groups(['people:list', 'people:item'])]
    private $last_view_addresses;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['people:list', 'people:item'])]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['people:list', 'people:item'])]
    private $slug;

    #[ORM\Column(type: 'string', length: 255, options: ["default" => "submitted"])]
    #[Groups(['people:list', 'people:item'])]
    private $state = 'submitted';

    public function __construct()
    {
        $this->phones = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->last_view_addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->second_name;
    }

    public function setSecondName(string $second_name): self
    {
        $this->second_name = $second_name;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middle_name;
    }

    public function setMiddleName(?string $middle_name): self
    {
        $this->middle_name = $middle_name;

        return $this;
    }

    public function getAddressResidental(): ?string
    {
        return $this->address_residental;
    }

    public function setAddressResidental(?string $address_residental): self
    {
        $this->address_residental = $address_residental;

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
        return $this->birthday_date;
    }

    public function setBirthdayDate(?\DateTimeImmutable $birthday_date): self
    {
        $this->birthday_date = $birthday_date;

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
        return $this->last_view_addresses;
    }

    public function addLastViewAddress(PeopleAddressLastView $lastViewAddress): self
    {
        if (!$this->last_view_addresses->contains($lastViewAddress)) {
            $this->last_view_addresses[] = $lastViewAddress;
            $lastViewAddress->setPeople($this);
        }

        return $this;
    }

    public function removeLastViewAddress(PeopleAddressLastView $lastViewAddress): self
    {
        if ($this->last_view_addresses->removeElement($lastViewAddress)) {
            // set the owning side to null (unless already changed)
            if ($lastViewAddress->getPeople() === $this) {
                $lastViewAddress->setPeople(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->first_name . ' ' . $this->second_name . ' ' . $this->middle_name;
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
            $this->slug = (string) $slugger->slug((string) $this)->lower();
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
}
