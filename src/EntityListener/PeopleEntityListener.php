<?php

namespace App\EntityListener;

use App\Entity\People;
use App\Message\PeopleStateMessage;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PeopleEntityListener
{
    private SluggerInterface $slugger;
    private MessageBusInterface $bus;

    public function __construct(SluggerInterface $slugger, MessageBusInterface $bus)
    {
        $this->slugger = $slugger;
        $this->bus = $bus;
    }

    public function prePersist(People $people, LifecycleEventArgs $event): void
    {
        $people->computeSlug($this->slugger);
    }

    public function postPersist(People $people, LifecycleEventArgs $event): void
    {
        $this->bus->dispatch(new PeopleStateMessage($people->getId()));
    }

    public function preUpdate(People $people, LifecycleEventArgs $event): void
    {
        $people->computeSlug($this->slugger);
    }
}