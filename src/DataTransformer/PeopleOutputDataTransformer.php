<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\PeopleOutput;
use App\Entity\People;

class PeopleOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param People $object
     */
    public function transform($object, string $to, array $context = []): PeopleOutput
    {
        return PeopleOutput::createFromEntity($object);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof People && $to === PeopleOutput::class;
    }
}
