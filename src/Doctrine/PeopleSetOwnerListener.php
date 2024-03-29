<?php

namespace App\Doctrine;

use App\Entity\People;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class PeopleSetOwnerListener
{
    public function __construct(private Security $security)
    {
    }

    public function prePersist(People $people): void
    {
        if ($people->getOwner()) {
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        if ($user !== null) {
            $people->setOwner($user);
        }
    }
}
