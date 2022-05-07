<?php
// TODO api platform > 2.6 \App\DataTransformer\PeopleInputDataTransformer::initialize
//namespace App\Serializer\Normalizer;
//
//use App\Dto\PeopleInput;
//use App\Entity\People;
//use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
//use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
//use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
//use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
//
//class PeopleInputDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
//{
//    private ObjectNormalizer $objectNormalizer;
//
//    public function __construct(ObjectNormalizer $objectNormalizer)
//    {
//        $this->objectNormalizer = $objectNormalizer;
//    }
//
//    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
//    {
//        $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $this->createDto($context);
//
//        return $this->objectNormalizer->denormalize($data, $type, $format, $context);
//    }
//
//    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
//    {
//        return $type === PeopleInput::class;
//    }
//
//    public function hasCacheableSupportsMethod(): bool
//    {
//        return true;
//    }
//
//    /**
//     * @throws \Exception
//     */
//    private function createDto(array $context): PeopleInput
//    {
//        $entity = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;
//
//        $dto = new PeopleInput();
//        // not an edit, so just return an empty DTO
//        if (!$entity) {
//            return $dto;
//        }
//
//        if (!$entity instanceof People) {
//            throw new \RuntimeException(sprintf('Unexpected resource class "%s"', get_class($entity)));
//        }
//
//        $dto->firstName = $entity->getFirstName();
//        $dto->secondName = $entity->getSecondName();
//        $dto->middleName = $entity->getMiddleName();
//        $dto->birthdayDate = $entity->getBirthdayDate();
//        $dto->addressResidental = $entity->getAddressResidental();
//        $dto->contacts = $entity->getContacts();
//        $dto->phones = $entity->getPhones();
//        $dto->photos = $entity->getPhotos();
//        $dto->lastViewAddresses = $entity->getLastViewAddresses();
//        $dto->createdAt = $entity->getCreatedAt();
//        $dto->slug = $entity->getSlug();
//        $dto->state = $entity->getState();
//        $dto->owner = $entity->getOwner();
//
//        return $dto;
//    }
//}
