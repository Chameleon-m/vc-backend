<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Service\StatsHelper;
use Traversable;
use ArrayIterator;

class DailyStatsPaginator implements PaginatorInterface, \IteratorAggregate
{
    private ?Traversable $dailyStatsIterator;
    private ?\DateTimeInterface $fromDate;

    public function __construct(
        private StatsHelper $statsHelper,
        private int $currentPage,
        private int $maxResults
    )
    {
    }

    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    public function getLastPage(): float
    {
        return ceil($this->getTotalItems() / $this->getItemsPerPage()) ?: 1.;
    }

    public function getTotalItems(): float
    {
        return $this->statsHelper->count();
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): float
    {
        return $this->maxResults;
    }

    public function getIterator(): Traversable
    {
        if ($this->dailyStatsIterator === null) {
            $offset = (($this->getCurrentPage() - 1) * $this->getItemsPerPage());

            $criteria = [];
            if ($this->fromDate) {
                $criteria['from'] = $this->fromDate;
            }

            $this->dailyStatsIterator = new ArrayIterator(
                $this->statsHelper->fetchMany(
                    $this->getItemsPerPage(),
                    $offset,
                    $criteria
                )
            );
        }
        return $this->dailyStatsIterator;
    }

    public function setFromDate(\DateTimeInterface $fromDate): void
    {
        $this->fromDate = $fromDate;
    }
}