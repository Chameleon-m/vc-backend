<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private DataPersisterInterface $decoratedDataPersister;
    private PasswordHasherFactoryInterface $userPasswordEncoder;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(
        DataPersisterInterface $decoratedDataPersister,
        PasswordHasherFactoryInterface $userPasswordEncoder,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    )
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     * @param array $context
     * @return User
     */
    public function persist($data, array $context = []): User
    {
        if (($context['item_operation_name'] ?? null) === 'put') {
            $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($data);

            $this->logger->info(sprintf('User "%s" is being updated!', $data->getId()));
        }

        // you can return a 403
//        throw new AccessDeniedException('Only admin users can unpublish');

        // new user
        if (!$data->getId()) {
            // take any actions needed for a new user send registration email integrate into some CRM or payment system
            $this->logger->info(sprintf('User %s just registered! Eureka!', $data->getEmail()));
        }

        if ($data->getPlainPassword()) {

            $data->setPassword(
                $this->userPasswordEncoder->getPasswordHasher(User::class)->hash($data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

        // old data persist
        return $this->decoratedDataPersister->persist($data);
    }

    /**
     * @param User $data
     * @param array $context
     * @return void
     */
    public function remove($data, array $context = []): void
    {
        $this->decoratedDataPersister->remove($data);
    }
}