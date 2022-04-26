<?php

namespace App\Api;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\People;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class FilterPublishedPeopleQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        string                      $operationName = null
    )
    {
        $this->addWhere($queryBuilder, $resourceClass, $operationName);
    }

    public function applyToItem(
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        array                       $identifiers,
        string                      $operationName = null,
        array                       $context = []
    )
    {
        $this->addWhere($queryBuilder, $resourceClass, $operationName);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, string $operationName = null): void
    {
        if ($resourceClass !== People::class) {
            return;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }
//        if ($operationName === 'get') {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf("%s.state = :state", $rootAlias))
            ->setParameter('state', 'published');

//        }

//        if (in_array($operationName, ['put', 'delete', 'patch'])) {
//            if ($this->security->getUser()) {
//                $queryBuilder->orWhere(sprintf("%s.owner = :owner", $rootAlias))
//                    ->setParameter('owner', $this->security->getUser());
//            }
//        }
    }
}