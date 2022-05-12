<?php

namespace App\EntityListener;

use App\Entity\People;
use App\Message\PeopleStateMessage;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PeopleEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
        private MessageBusInterface $bus,
        private UrlGeneratorInterface $router
    ) {
    }

    public function prePersist(People $people, LifecycleEventArgs $event): void
    {
        $people->computeSlug($this->slugger);
    }

    public function postPersist(People $people, LifecycleEventArgs $event): void
    {
        $reviewUrl = $this->router->generate('review_people', ['id' => $people->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->bus->dispatch(new PeopleStateMessage($people->getId(), $reviewUrl));
    }

    public function preUpdate(People $people, LifecycleEventArgs $event): void
    {
        $people->computeSlug($this->slugger);
    }
}