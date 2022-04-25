<?php

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'USER_DENORMALIZER_ALREADY_CALLED';

    private Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param User $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return mixed
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        if ($this->userIsOwner($data)) {
            $context['groups'][] = 'owner:write';
        }

        $context[self::ALREADY_CALLED] = true;

        $data = $this->denormalizer->denormalize($data, $type, $format, $context);

        // Here: add, edit, or delete some data

        return $data;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        // avoid recursion: only call once per object
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }
        return $data instanceof User;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }

    private function userIsOwner(User $user): bool
    {
        /** @var User|null $authenticatedUser */
        $authenticatedUser = $this->security->getUser();
        if (!$authenticatedUser) {
            return false;
        }
        return $authenticatedUser->getId() === $user->getId();
    }
}
