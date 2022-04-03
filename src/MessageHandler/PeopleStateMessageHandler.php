<?php

namespace App\MessageHandler;

use App\Message\PeopleStateMessage;
use App\Repository\PeopleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PeopleStateMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private PeopleRepository $peopleRepository;

    public function __construct(EntityManagerInterface $entityManager, PeopleRepository $peopleRepository)
    {
        $this->entityManager = $entityManager;
        $this->peopleRepository = $peopleRepository;
    }

    public function __invoke(PeopleStateMessage $message)
    {
        $people = $this->peopleRepository->find($message->getId());
        if (!$people) {
            return;
        }

        // todo
        $people->setState('published');

        $this->entityManager->flush();
    }
}
