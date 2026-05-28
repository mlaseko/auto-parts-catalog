<?php

namespace App\Repository\Catalog;

use Doctrine\DBAL\Connection;

class CrossReferenceRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Return all cross references for a given oitm record.
     *
     * @return list<array<string,mixed>>
     */
    public function findByOitmId(int $oitmId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, oem_number, oem_number_normalized, manufacturer_name,
                    manufacturer_id, source, created_at, reference_type
               FROM oitm_cross_reference
              WHERE oitm_id = :id
              ORDER BY reference_type, manufacturer_name',
            ['id' => $oitmId]
        );
    }
}
