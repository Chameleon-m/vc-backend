<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserDataPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private PasswordHasherFactoryInterface $userPasswordEncoder;

    public function __construct(EntityManagerInterface $entityManager, PasswordHasherFactoryInterface $userPasswordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     * @return User
     */
    public function persist($data)
    {
        if ($data->getPlainPassword()) {

            $data->setPassword(
                $this->userPasswordEncoder->getPasswordHasher(User::class)->hash($data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param User $data
     * @return void
     */
    public function remove($data): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}