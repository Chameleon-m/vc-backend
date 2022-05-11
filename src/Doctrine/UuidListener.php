<?php

namespace App\Doctrine;

use Symfony\Component\Uid\Factory\UuidFactory;

class UuidListener
{
    private UuidFactory $uuidGenerator;

    public function __construct(UuidFactory $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    public function prePersist($entity): void
    {
        if (!$entity instanceof UuidListenerInterface) {
            throw new \RuntimeException($entity::class . ' must be implement App\Doctrine\UuidListenerInterface');
        }
        $entity->getUuid() === null && $entity->setUuid($this->uuidGenerator->create());
    }
}
