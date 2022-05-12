<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\DailyStats;
use Psr\Log\LoggerInterface;


class DailyStatsPersister implements DataPersisterInterface
{
    public function __construct(private LoggerInterface $logger)
    {
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
