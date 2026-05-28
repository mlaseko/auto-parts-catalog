<?php

namespace App\Repository\Catalog;

use Doctrine\DBAL\Connection;

class CategoryRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /** @return list<string> */
    public function findPartGroups(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT DISTINCT part_group
               FROM oitm
              WHERE part_group IS NOT NULL
              ORDER BY part_group'
        );

        return array_column($rows, 'part_group');
    }

    /** @return list<string> */
    public function findPartComponentsByGroup(string $partGroup): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT DISTINCT part_component
               FROM oitm
              WHERE part_group = :pg
                AND part_component IS NOT NULL
              ORDER BY part_component',
            ['pg' => $partGroup]
        );

        return array_column($rows, 'part_component');
    }
}
