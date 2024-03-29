<?php

namespace App\Service;

use App\Entity\DailyStats;
use App\Repository\PeopleRepository;

class StatsHelper
{
    public function __construct(private PeopleRepository $peopleRepository)
    {
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array An array of criteria to limit the results
     *              Supported keys are:
     *                  * from DateTimeInterface
     *                  * to   DateTimeInterface
     * @return array|DailyStats[]
     * @throws \Exception
     */
    public function fetchMany(int $limit = null, int $offset = null, array $criteria = []): array
    {
        $fromDate = $criteria['from'] ?? null;
        $toDate = $criteria['to'] ?? null;

        $i = 0;
        $stats = [];
        foreach ($this->fetchStatsData() as $statData) {
            $i++;
            if ($offset >= $i) {

                continue;
            }

            $dateString = $statData['date'];
            $date = new \DateTimeImmutable($dateString);

            if ($fromDate && $date < $fromDate) {
                continue;
            }

            if ($toDate && $date > $toDate) {
                continue;
            }

            $stats[$dateString] = $this->createStatsObject($statData);

            if (count($stats) >= $limit) {
                break;
            }
        }

        return $stats;
    }

    public function fetchOne(string $date): ?DailyStats
    {
        foreach ($this->fetchStatsData() as $statData) {
            if ($statData['date'] === $date) {
                return $this->createStatsObject($statData);
            }
        }

        return null;
    }

    public function count(): int
    {
        return count($this->fetchStatsData());
    }

    /**
     * @throws \JsonException
     */
    private function fetchStatsData(): array
    {
        $statsData = json_decode(file_get_contents(__DIR__ . '/fake_stats.json'), true, 512, JSON_THROW_ON_ERROR);

        return $statsData['stats'];
    }

    private function getRandomItems(array $items, int $max): array
    {
        if ($max > count($items)) {
            shuffle($items);

            return $items;
        }

        $finalItems = [];
        while (count($finalItems) < $max) {
            $item = $items[array_rand($items)];
            if (!in_array($item, $finalItems)) {
                $finalItems[] = $item;
            }
        }

        return $finalItems;
    }

    private function createStatsObject(array $statData): DailyStats
    {
        $listings = $this->peopleRepository
            ->findBy([], [], 10);

        return new DailyStats(
            new \DateTimeImmutable($statData['date']),
            $statData['visitors'],
            $this->getRandomItems($listings, 5)
        );
    }
}
