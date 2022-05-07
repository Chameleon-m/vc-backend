<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\DailyStats;
use Psr\Log\LoggerInterface;


class DailyStatsPersister implements DataPersisterInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function supports($data): bool
    {
        return $data instanceof DailyStats;
    }

    /**
     * @param DailyStats $data
     * @return DailyStats
     */
    public function persist($data): DailyStats
    {
        $this->logger->info(sprintf('Update the visitors to "%d"', $data->totalVisitors));
        return $data;
    }

    public function remove($data)
    {
        throw new \RuntimeException('not supported!');
    }
}
