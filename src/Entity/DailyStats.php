<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: [
        'get',
//        'get' => [
//            "method" => "GET",
//            "controller" => NotFoundAction::class,
//            "read" => false,
//            "output" => false,
//        ]
        'put',
    ],
    shortName: "daily-stats",
    denormalizationContext: [
        'groups' => ['daily-stats:read']
    ],
    normalizationContext: [
        'groups' => ['daily-stats:read']
    ],
    paginationItemsPerPage: 5,
)]
class DailyStats
{
    #[Groups(['daily-stats:read'])]
    public \DateTimeInterface $date;

    #[Groups(['daily-stats:read', 'daily-stats:write'])]
    public int $totalVisitors;

    #[Groups(['daily-stats:read'])]
    /**
     * 5 most popular people
     * @var array<People>
     */
    public array $mostPopularListings;

    /**
     * @param array|People[] $mostPopularListings
     */
    public function __construct(\DateTimeInterface $date, int $totalVisitors, array $mostPopularListings)
    {
        $this->date = $date;
        $this->totalVisitors = $totalVisitors;
        $this->mostPopularListings = $mostPopularListings;
    }

    #[ApiProperty(identifier: true)]
    public function getDateString(): string
    {
        return $this->date->format('Y-m-d');
    }
}