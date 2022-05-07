<?php

namespace App\Dto;

use App\Entity\People;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

class PeopleOutput
{
    #[Groups(['people:collection:read', 'people:item:read'])]
    public string $firstName;

    #[Groups(['people:collection:read', 'people:item:read'])]
    public string $secondName;

    #[Groups(['people:collection:read', 'people:item:read'])]
    public ?string $middleName;

    #[Groups(['people:collection:read', 'people:item:read'])]
    public ?\DateTimeImmutable $birthdayDate;

    #[Groups(['people:collection:read', 'people:item:read'])]
    public ?string $addressResidental;

    #[Groups(['people:collection:read', 'people:item:read'])]
    public array $contacts;

    #[Groups(['people:collection:read', 'people:item:read'])]
    public Collection $phones;

    #[Groups(['people:collection:read', 'people:item:read'])]
    public Collection $photos;

    #[Groups(['people:collection:read', 'people:item:read'])]
    public Collection $lastViewAddresses;

    #[Groups(['people:read'])]
    public ?\DateTimeImmutable $createdAt;

    #[Groups(['people:read'])]
    public string $slug;

    #[Groups(['people:read'])]
    public string $state = 'submitted';

    #[Groups(['people:read'])]
    public User $owner;

    public static function createFromEntity(People $entity): self
    {
        $dto = new PeopleOutput();
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
