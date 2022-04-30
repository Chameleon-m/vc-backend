<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\ApiPlatform\DailyStatsDateFilter;
use App\Entity\DailyStats;
use App\Service\StatsHelper;

class DailyStatsProvider implements ContextAwareCollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private StatsHelper $statsHelper;
    private Pagination $pagination;

    public function __construct(StatsHelper $statsHelper, Pagination $pagination)
    {
        $this->statsHelper = $statsHelper;
        $this->pagination = $pagination;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        list($page, $offset, $limit) = $this->pagination->getPagination($resourceClass, $operationName, $context);
        $paginator = new DailyStatsPaginator(
            $this->statsHelper,
            $page,
            $limit
        );
        $fromDate = $context[DailyStatsDateFilter::FROM_FILTER_CONTEXT] ?? null;
        if ($fromDate) {
            $paginator->setFromDate($fromDate);
        }
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?DailyStats
    {
        return $this->statsHelper->fetchOne($id);
    }
}
