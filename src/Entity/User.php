<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\UuidListener;
use App\Doctrine\UuidListenerInterface;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

//#[ORM\HasLifecycleCallbacks]
#[ORM\EntityListeners([UuidListener::class])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_USER')"
        ],
        'post' => [
//            'security' => "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
            'validation_groups' => ["Default", "create"]
        ]
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('ROLE_ADMIN') or object == user"
        ],
        'put' => [
            'security' => "is_granted('ROLE_ADMIN') or object == user",
        ],
        'delete' => [
            'security' => "is_granted('ROLE_ADMIN') or object == user"
        ],
        'patch' => [
            'security' => "is_granted('ROLE_ADMIN') or object == user"
        ]
    ],
//    security: "is_granted('ROLE_USER') or object == user",
//    normalizationContext: ['jsonld_embed_context' => true],
)]
#[ApiFilter(
    SearchFilter::class, properties: [
        'email' => 'start',
        'phone' => 'start',
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, UuidListenerInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['user:read', 'admin:read', 'admin:write'])]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
//    #[ApiProperty(
//        readableLink: false,
////        writableLink: false
//    )]
    private ?string $password = null;

    //https://symfonycasts.com/screencast/api-platform-security/context-builder#making-roles-writeable-by-only-an-admin
    #[Groups(['admin:write', 'owner:write', 'user:write'])]
    #[SerializedName('password')]
    #[Assert\NotBlank(groups: ['create'])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: People::class, orphanRemoval: true)]
    #[Groups(['user:write', 'admin:read', 'admin:write'])]
    #[Assert\Valid]
    private Collection $people;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['user:read', 'user:collection:post'])]
    #[ApiProperty(identifier: true)]
    #[SerializedName("id")]
//    #[ORM\GeneratedValue(strategy: "CUSTOM")]
//    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $uuid = null;

    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, People>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    // @ApiProperty(readableLink=true)
    // @ApiProperty(readableLink=false) for IRI
    /**
     * @Groups({"user:read"})
     * @SerializedName("people")
     * @return Collection<int, People>
     */
    public function getPeoplePublished(): Collection
    {
        // https://symfonycasts.com/screencast/doctrine-relations/collection-criteria
        return $this->people->filter(function(People $people) {
            return $people->getState() === 'published';
        });
    }

    public function addPerson(People $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $person->setOwner($this);
        }

        return $this;
    }

    public function removePerson(People $person): self
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getOwner() === $this) {
                $person->setOwner(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }


//    #[ORM\PrePersist]
//    public function setUuidValue()
//    {
//        $this->uuid = Uuid::v6();
//    }
}
