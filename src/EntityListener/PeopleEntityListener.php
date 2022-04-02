<?php

namespace App\EntityListener;

use App\Entity\People;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class PeopleEntityListener
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(People $people, LifecycleEventArgs $event): void
    {
        $people->computeSlug($this->slugger);
    }

    public function preUpdate(People $people, LifecycleEventArgs $event): void
    {
        $people->computeSlug($this->slugger);
    }
}