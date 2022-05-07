<?php

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
    public Collection $phones;

    #[Groups(['people:collection:write', 'people:item:write'])]
    public Collection $photos;

    #[Assert\Valid]
    #[Groups(['people:collection:write', 'people:item:write'])]
    public Collection $lastViewAddresses;

    #[Groups(['admin:write'])]
    public ?\DateTimeImmutable $createdAt;

    #[Groups(['admin:write'])]
    public ?string $slug;

    #[Groups(['admin:write'])]
    public string $state = 'submitted';

    #[IsValidOwner]
    #[Groups(['people:collection:post', 'admin:write'])]
    public ?User $owner = null;

    public function createOrUpdateEntity(?People $people): People
    {
        if (!$people) {
            $people = new People();
        }

        $people->setFirstName($this->firstName);
        $people->setSecondName($this->secondName);
        $people->setMiddleName($this->middleName);
        $people->setBirthdayDate($this->birthdayDate);
        $people->setAddressResidental($this->addressResidental);
        $people->setContacts($this->contacts);
//        $people->setPhones($this->phones);
//        $people->setPhotos($this->photos);
//        $people->setLastViewAddresses($this->lastViewAddresses);
//        $people->setCreatedAt($this->createdAt);
//        $people->setSlug($this->slug);
//        $people->setState($this->state);
        $people->setOwner($this->owner);

        return $people;
    }

    public static function createFromEntity(?People $entity): self
    {
        $dto = new self();
        // not an edit, so just return an empty DTO
        if (!$entity) {
            return $dto;
        }

        $dto->firstName = $entity->getFirstName();
        $dto->secondName = $entity->getSecondName();
        $dto->middleName = $entity->getMiddleName();
        $dto->birthdayDate = $entity->getBirthdayDate();
        $dto->addressResidental = $entity->getAddressResidental();
        $dto->contacts = $entity->getContacts();
        $dto->phones = $entity->getPhones();
        $dto->photos = $entity->getPhotos();
        $dto->lastViewAddresses = $entity->getLastViewAddresses();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->slug = $entity->getSlug();
        $dto->state = $entity->getState();
        $dto->owner = $entity->getOwner();

        return $dto;
    }
}
