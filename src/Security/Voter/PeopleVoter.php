<?php

namespace App\Security\Voter;

use App\Entity\People;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PeopleVoter extends Voter
{
    public const EDIT = 'PEOPLE_ITEM_EDIT';
    public const DELETE = 'PEOPLE_ITEM_DELETE';
    public const PATCH = 'PEOPLE_ITEM_PATCH';

    public function __construct(private Security $security)
    {
    }

    /**
     * @param string $attribute
     * @param People|null $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE, self::PATCH])
            && $subject instanceof People;
    }

    // CacheableVoterInterface
    // TODO supportsAttribute
    // TODO supportsType

    /**
     * @param string $attribute
     * @param People|null $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT,
            self::DELETE,
            self::PATCH => $this->security->isGranted('ROLE_ADMIN') || $subject->getOwner() === $user,

            default => throw new \RuntimeException(sprintf('Unhandled attribute "%s"', $attribute)),
        };
    }
}
