<?php

namespace App\Doctrine;

use Symfony\Component\Uid\Uuid;

interface UuidListenerInterface
{
    public function setUuid(Uuid $uuid): self;
}
