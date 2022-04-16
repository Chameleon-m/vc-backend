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
    private EntityManagerInterface $entityManager;
    private PeopleRepository $peopleRepository;
    private MessageBusInterface $bus;
    private WorkflowInterface $workflow;
    private NotifierInterface $notifier;
    private ImageOptimizer $imageOptimizer;
    private string $peoplePhotoDirRealPath;
    private KernelInterface $kernel;
    private ?LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        PeopleRepository       $peopleRepository,
        MessageBusInterface    $bus,
        WorkflowInterface      $peopleStateMachine,
        NotifierInterface      $notifier,
        ImageOptimizer         $imageOptimizer,
        string                 $peoplePhotoDirRealPath,
        KernelInterface        $kernel,
        LoggerInterface        $logger = null
    )
    {
        $this->entityManager = $entityManager;
        $this->peopleRepository = $peopleRepository;
        $this->bus = $bus;
        $this->workflow = $peopleStateMachine;
        $this->notifier = $notifier;
        $this->imageOptimizer = $imageOptimizer;
        $this->peoplePhotoDirRealPath = $peoplePhotoDirRealPath;
        $this->kernel = $kernel;
        $this->logger = $logger;
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
            if ($isEnvTest) {
                $this->workflow->apply($people, 'publish');
                $this->entityManager->flush();
                $this->bus->dispatch(new PeopleStateMessage($people->getId(), $message->getReviewUrl()));
            } else {
                $notification = new PeopleReviewNotification($people, $message->getReviewUrl());
                $this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
            }
        } elseif ($this->workflow->can($people, 'publish_ham')) {
            if ($isEnvTest) {
                $this->workflow->apply($people, 'publish_ham');
                $this->entityManager->flush();
                $this->bus->dispatch(new PeopleStateMessage($people->getId(), $message->getReviewUrl()));
            } else {
                $notification = new PeopleReviewNotification($people, $message->getReviewUrl());
                $this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
            }
        } elseif ($this->workflow->can($people, 'optimize')) {
            $photos = $people->getPhotos();
            if (!$photos->isEmpty()) {
                foreach ($photos as $photo) {
                    $photoPath = $this->peoplePhotoDirRealPath . '/' . $photo->getFilename();
                    $photoResizePath = $this->peoplePhotoDirRealPath . '/resize_' . $photo->getFilename();
                    $this->imageOptimizer->resize($photoPath, $photoResizePath);
                }
            }
            $this->workflow->apply($people, 'optimize');
            $this->entityManager->flush();

        } elseif ($this->logger) {
            $this->logger->debug('Dropping people message', ['people' => $people->getId(), 'state' => $people->getState()]);
        }
    }
}
