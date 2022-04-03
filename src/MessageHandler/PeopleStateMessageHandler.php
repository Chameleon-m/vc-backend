<?php

namespace App\MessageHandler;

use App\Message\PeopleStateMessage;
use App\Repository\PeopleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class PeopleStateMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private PeopleRepository $peopleRepository;
    private MessageBusInterface $bus;
    private WorkflowInterface $workflow;
    private ?LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        PeopleRepository       $peopleRepository,
        MessageBusInterface    $bus,
        WorkflowInterface      $peopleStateMachine,
        LoggerInterface        $logger = null
    )
    {
        $this->entityManager = $entityManager;
        $this->peopleRepository = $peopleRepository;
        $this->bus = $bus;
        $this->workflow = $peopleStateMachine;
        $this->logger = $logger;
    }

    public function __invoke(PeopleStateMessage $message)
    {
        $people = $this->peopleRepository->find($message->getId());
        if (!$people) {
            return;
        }

        if ($this->workflow->can($people, 'accept')) {

            $transition = 'accept';

            // todo
//            $score = $this->spamChecker->getSpamScore($people, $message->getContext());
//            if (2 === $score) {
//                $transition = 'reject_spam';
//            } elseif (1 === $score) {
//                $transition = 'might_be_spam';
//            }

            $this->workflow->apply($people, $transition);
            $this->entityManager->flush();

            $this->bus->dispatch($message);

        } elseif ($this->workflow->can($people, 'publish')) {
            $this->workflow->apply($people, 'publish');
            $this->entityManager->flush();
        } elseif ($this->workflow->can($people, 'publish_ham')) {
            $this->workflow->apply($people, 'publish_ham');
            $this->entityManager->flush();
        } elseif ($this->logger) {
            $this->logger->debug('Dropping people message', ['comment' => $people->getId(), 'state' => $people->getState()]);
        }
    }
}
