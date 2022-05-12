<?php

namespace App\MessageHandler;

use App\Service\ImageOptimizer;
use App\Message\PeopleStateMessage;
use App\Notification\PeopleReviewNotification;
use App\Repository\PeopleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class PeopleStateMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PeopleRepository       $peopleRepository,
        private MessageBusInterface    $bus,
        private WorkflowInterface      $peopleStateMachine,
        private NotifierInterface      $notifier,
        private ImageOptimizer         $imageOptimizer,
        private string                 $peoplePhotoDirRealPath,
        private KernelInterface        $kernel,
        private ?LoggerInterface       $logger = null
    )
    {
    }

    public function __invoke(PeopleStateMessage $message)
    {
        $isEnvTest = $this->kernel->getEnvironment() === 'test';

        $peopleId = $message->getId();

        $people = $this->peopleRepository->find($peopleId);
        if (!$people) {
            $this->logger?->alert(sprintf('People %d was missing!', $peopleId));
            return;
        }

        if ($this->peopleStateMachine->can($people, 'accept')) {

            $transition = 'accept';

            // todo
//            $score = $this->spamChecker->getSpamScore($people, $message->getContext());
//            if (2 === $score) {
//                $transition = 'reject_spam';
//            } elseif (1 === $score) {
//                $transition = 'might_be_spam';
//            }

            $this->peopleStateMachine->apply($people, $transition);
            $this->entityManager->flush();

            $this->bus->dispatch($message);

        } elseif ($this->peopleStateMachine->can($people, 'publish')) {
            if ($isEnvTest) {
                $this->peopleStateMachine->apply($people, 'publish');
                $this->entityManager->flush();
                $this->bus->dispatch(new PeopleStateMessage($people->getId(), $message->getReviewUrl()));
            } else {
                $notification = new PeopleReviewNotification($people, $message->getReviewUrl());
                $this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
            }
        } elseif ($this->peopleStateMachine->can($people, 'publish_ham')) {
            if ($isEnvTest) {
                $this->peopleStateMachine->apply($people, 'publish_ham');
                $this->entityManager->flush();
                $this->bus->dispatch(new PeopleStateMessage($people->getId(), $message->getReviewUrl()));
            } else {
                $notification = new PeopleReviewNotification($people, $message->getReviewUrl());
                $this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
            }
        } elseif ($this->peopleStateMachine->can($people, 'optimize')) {
            $photos = $people->getPhotos();
            if (!$photos->isEmpty()) {
                foreach ($photos as $photo) {
                    $photoPath = $this->peoplePhotoDirRealPath . '/' . $photo->getFilename();
                    $photoResizePath = $this->peoplePhotoDirRealPath . '/resize_' . $photo->getFilename();
                    $this->imageOptimizer->resize($photoPath, $photoResizePath);
                }
            }
            $this->peopleStateMachine->apply($people, 'optimize');
            $this->entityManager->flush();

        } elseif ($this->logger) {
            $this->logger->debug('Dropping people message', ['people' => $people->getId(), 'state' => $people->getState()]);
        }
    }
}
