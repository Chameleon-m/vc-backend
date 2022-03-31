<?php

namespace App\Entity;

use App\Repository\PeopleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeopleRepository::class)]
class People
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $first_name;

    #[ORM\Column(type: 'string', length: 255)]
    private $second_name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $middle_name;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private $birthday_date;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address_residental;

    #[ORM\Column(type: 'string', length: 255)]
    private $contacts;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeoplePhone::class, orphanRemoval: true)]
    private $phones;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeoplePhoto::class, orphanRemoval: true)]
    private $photos;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PeopleAddressLastView::class, orphanRemoval: true)]
    private $last_view_addresses;

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

    public function getContacts(): ?string
    {
        return $this->contacts;
    }

    public function setContacts(string $contacts): self
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
}
