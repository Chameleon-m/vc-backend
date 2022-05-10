<?php

declare(strict_types=0);

namespace App\Dto;

use App\Entity\People;
use App\Entity\User;
use App\Validator\IsValidOwner;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PeopleInput
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(
        min: 3,
        max: 255,
//        minMessage: '',
//        maxMessage: ''
    )]
    #[Groups(['people:write','people:collection:write', 'people:item:write'])]
    public string $firstName;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[Groups(['people:collection:write', 'people:item:write'])]
    public string $secondName;

    #[Groups(['people:collection:write', 'people:item:write'])]
    public ?string $middleName;

    #[Groups(['people:collection:write', 'people:item:write'])]
    public ?\DateTimeImmutable $birthdayDate;

    #[Groups(['people:collection:write', 'people:item:write'])]
    public ?string $addressResidental;

    #[Assert\NotBlank]
    #[Groups(['people:collection:write', 'people:item:write'])]
    public array $contacts;

    #[Groups(['people:collection:write', 'people:item:write'])]
    public ?Collection $phones;

    #[Groups(['people:collection:write', 'people:item:write'])]
    public ?Collection $photos;

    #[Assert\Valid]
    #[Groups(['people:collection:write', 'people:item:write'])]
    public ?Collection $lastViewAddresses;

    #[Groups(['admin:write'])]
    public ?\DateTimeImmutable $createdAt;

    #[Groups(['admin:write'])]
    public ?string $slug;

    #[Groups(['admin:write'])]
    public ?string $state;

    #[IsValidOwner]
    #[Groups(['people:collection:post', 'admin:write'])]
    public ?User $owner;

    public function createOrUpdateEntity(?People $people): People
    {
        if (!$people) {
            $people = new People();
        }

        $people->setFirstName($this->getFirstName());
        $people->setSecondName($this->getSecondName());
        $people->setMiddleName($this->getMiddleName());
        $people->setBirthdayDate($this->getBirthdayDate());
        $people->setAddressResidental($this->getAddressResidental());
        $people->setContacts($this->getContacts());
//        $this->getPhones() && $people->setPhones($this->phones);
//        $this->getPhotos() && $people->setPhotos($this->photos);
//        $this->getLastViewAddresses() && $people->setLastViewAddresses($this->lastViewAddresses);
        $this->getCreatedAt() && $people->setCreatedAt($this->getCreatedAt());
        $this->getSlug() && $people->setSlug($this->getSlug());
        $this->getState() && $people->setState($this->getState());
        $people->setOwner($this->getOwner());

        return $people;
    }

    public static function createFromEntity(?People $entity): self
    {
        $dto = new self();
        // not an edit, so just return an empty DTO
        if (!$entity) {
            return $dto;
        }

        $dto->setFirstName($entity->getFirstName());
        $dto->setSecondName($entity->getSecondName());
        $dto->setMiddleName($entity->getMiddleName());
        $dto->setBirthdayDate($entity->getBirthdayDate());
        $dto->setAddressResidental($entity->getAddressResidental());
        $dto->setContacts($entity->getContacts());
//        $dto->phones = $entity->getPhones();
//        $dto->photos = $entity->getPhotos();
//        $dto->lastViewAddresses = $entity->getLastViewAddresses();
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setSlug($entity->getSlug());
        $dto->setState($entity->getState());
        $dto->setOwner($entity->getOwner());

        return $dto;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName ?? null;
    }

    public function setFirstName(string $firstName): PeopleInput
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->secondName ?? null;
    }

    public function setSecondName(string $secondName): PeopleInput
    {
        $this->secondName = $secondName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName ?? null;
    }

    /**
     * @param string|null $middleName
     * @return PeopleInput
     */
    public function setMiddleName(?string $middleName): PeopleInput
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getBirthdayDate(): ?\DateTimeImmutable
    {
        return $this->birthdayDate ?? null;
    }

    /**
     * @param \DateTimeImmutable|null $birthdayDate
     * @return PeopleInput
     */
    public function setBirthdayDate(?\DateTimeImmutable $birthdayDate): PeopleInput
    {
        $this->birthdayDate = $birthdayDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressResidental(): ?string
    {
        return $this->addressResidental ?? null;
    }

    /**
     * @param string|null $addressResidental
     * @return PeopleInput
     */
    public function setAddressResidental(?string $addressResidental): PeopleInput
    {
        $this->addressResidental = $addressResidental;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getContacts(): ?array
    {
        return $this->contacts ?? null;
    }

    /**
     * @param array|null $contacts
     * @return PeopleInput
     */
    public function setContacts(?array $contacts): PeopleInput
    {
        $this->contacts = $contacts;
        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getPhones(): ?Collection
    {
        return $this->phones ?? null;
    }

    /**
     * @param Collection|null $phones
     * @return PeopleInput
     */
    public function setPhones(?Collection $phones): PeopleInput
    {
        $this->phones = $phones;
        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getPhotos(): ?Collection
    {
        return $this->photos ?? null;
    }

    /**
     * @param Collection|null $photos
     * @return PeopleInput
     */
    public function setPhotos(?Collection $photos): PeopleInput
    {
        $this->photos = $photos;
        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getLastViewAddresses(): ?Collection
    {
        return $this->lastViewAddresses ?? null;
    }

    /**
     * @param Collection|null $lastViewAddresses
     * @return PeopleInput
     */
    public function setLastViewAddresses(?Collection $lastViewAddresses): PeopleInput
    {
        $this->lastViewAddresses = $lastViewAddresses;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt ?? null;
    }

    /**
     * @param \DateTimeImmutable|null $createdAt
     * @return PeopleInput
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): PeopleInput
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug ?? null;
    }

    /**
     * @param string|null $slug
     * @return PeopleInput
     */
    public function setSlug(?string $slug): PeopleInput
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state ?? null;
    }

    /**
     * @param string|null $state
     * @return PeopleInput
     */
    public function setState(?string $state): PeopleInput
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner ?? null;
    }

    /**
     * @param User|null $owner
     * @return PeopleInput
     */
    public function setOwner(?User $owner): PeopleInput
    {
        $this->owner = $owner;
        return $this;
    }

}
