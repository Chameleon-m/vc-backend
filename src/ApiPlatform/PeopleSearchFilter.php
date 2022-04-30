<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PeopleSearchFilter extends AbstractFilter
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        ?RequestStack $requestStack = null,
        LoggerInterface $logger = null,
        array $properties = null,
        NameConverterInterface $nameConverter = null,
        bool $useLike = false
    )
    {
        // todo - actually use this
        $this->useLike = $useLike;
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    )
    {
        if ($property !== 'search') {
            return;
        }

        // a param name that is guaranteed unique in this query
        $valueParameter = $queryNameGenerator->generateParameterName('search');
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.firstName LIKE :%s OR %s.secondName LIKE :%s', $alias, $valueParameter, $alias, $valueParameter))
            ->setParameter($valueParameter, '%'.$value.'%');

//        $queryBuilder->andWhere(sprintf('%s.firstName LIKE :search OR %s.secondName LIKE :search', $alias, $alias))
//            ->setParameter('search', '%'.$value.'%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'description' => 'Search across multiple fields',
            ]
        ];
    }
}
