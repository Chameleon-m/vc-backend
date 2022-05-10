<?php
// TODO api platform > 2.6 \App\DataTransformer\PeopleInputDataTransformer::initialize
//namespace App\Serializer\Normalizer;
//
//use App\Dto\PeopleInput;
//use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
//use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
//use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
//use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
//use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
//
//class PeopleInputDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
//{
//    use DenormalizerAwareTrait;
//
//    private const ALREADY_CALLED = 'PEOPLE_DENORMALIZER_ALREADY_CALLED';
//
//    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
//    {
//        $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $this->createDto($context);
//        $context[self::ALREADY_CALLED] = true;
//
//        return $this->denormalizer->denormalize($data, $type, $format, $context);
//    }
//
//    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
//    {
//        // avoid recursion: only call once per object
//        if (isset($context[self::ALREADY_CALLED])) {
//            return false;
//        }
//        return $type === PeopleInput::class;
//    }
//
//    public function hasCacheableSupportsMethod(): bool
//    {
//        return false;
//    }
//
//    private function createDto(array $context): PeopleInput
//    {
//        $entity = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;
//        return PeopleInput::createFromEntity($entity);
//    }
//}