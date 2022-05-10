<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\PeopleInput;
use App\Entity\People;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PeopleInputDataTransformer implements DataTransformerInterface, DataTransformerInitializerInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param PeopleInput $input
     * @param string $to
     * @param array $context
     * @return People
     */
    public function transform($input, string $to, array $context = []): People
    {
        $this->validator->validate($input);
        $people = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        return $input->createOrUpdateEntity($people);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof People) {
            // already transformed
            return false;
        }

        return $to === People::class && ($context['input']['class'] ?? null) === PeopleInput::class;
    }

    // TODO delete PeopleInputDenormalize
    // https://symfonycasts.com/screencast/api-platform-extending/input-initializer-logic#object-to-populate-cheeselisting-amp-cheeselistinginput
    public function initialize(string $inputClass, array $context = []): PeopleInput
    {
        $entity = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;

        if ($entity && !$entity instanceof People) {
            throw new \RuntimeException(sprintf('Unexpected resource class "%s"', get_class($entity)));
        }

        return PeopleInput::createFromEntity($entity);
    }
}
