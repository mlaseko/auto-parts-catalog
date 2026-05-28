<?php

namespace App\Repository\Catalog;

use Doctrine\DBAL\Connection;

class OitmRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * List products for a given part_group / part_component combination.
     * An optional vehicleId narrows results to compatible vehicles.
     *
     * @return list<array<string,mixed>>
     */
    public function findByGroupAndComponent(
        string $partGroup,
        string $partComponent,
        ?int $vehicleId = null,
        int $limit = 50,
        int $offset = 0,
    ): array {
        $params = ['pg' => $partGroup, 'pc' => $partComponent];
        $types  = [];

        $sql = 'SELECT DISTINCT ON (o.id)
                    o.id, o.name, o.supplier_name, o.item_code, o.article_number,
                    o.canonical_oem_number, o.image_url, o.stock_on_hand,
                    o.sell_price_tzs, o.part_group, o.part_component,
                    o.compatible_vehicles_count, o.part_group_confidence
               FROM oitm o';

        if ($vehicleId !== null) {
            $sql .= ' INNER JOIN oitm_compatible_vehicle cv ON cv.oitm_id = o.id AND cv.vehicle_id = :vid';
            $params['vid'] = $vehicleId;
        }

        $sql .= ' WHERE o.part_group = :pg AND o.part_component = :pc
                  ORDER BY o.id,
                           (o.stock_on_hand > 0) DESC,
                           o.compatible_vehicles_count DESC NULLS LAST
                  LIMIT :lim OFFSET :off';

        $params['lim'] = $limit;
        $params['off'] = $offset;

        return $this->connection->fetchAllAssociative($sql, $params, $types);
    }

    /**
     * Find a single product with all key columns.
     *
     * @return array<string,mixed>|null
     */
    public function findById(int $id): ?array
    {
        $row = $this->connection->fetchAssociative(
            'SELECT o.*
               FROM oitm o
              WHERE o.id = :id',
            ['id' => $id]
        );

        return $row ?: null;
    }

    /**
     * Search products by normalised OEM / item-code / article-number.
     * Also searches oitm_cross_reference.oem_number_normalized.
     *
     * @return list<array<string,mixed>>
     */
    public function search(string $normalised, ?int $vehicleId = null, int $limit = 50): array
    {
        $params = [
            'q'   => $normalised,
            'lim' => $limit,
        ];

        $vehicleJoin = '';
        if ($vehicleId !== null) {
            $vehicleJoin = ' INNER JOIN oitm_compatible_vehicle cv ON cv.oitm_id = o.id AND cv.vehicle_id = :vid';
            $params['vid'] = $vehicleId;
        }

        $sql = "SELECT DISTINCT ON (o.id)
                    o.id, o.name, o.supplier_name, o.item_code, o.article_number,
                    o.canonical_oem_number, o.image_url, o.stock_on_hand,
                    o.sell_price_tzs, o.part_group, o.part_component,
                    o.compatible_vehicles_count, o.part_group_confidence
               FROM oitm o
               LEFT JOIN oitm_cross_reference xr ON xr.oitm_id = o.id
               {$vehicleJoin}
              WHERE o.canonical_oem_number = :q
                 OR o.article_number       = :q
                 OR o.item_code            = :q
                 OR xr.oem_number_normalized = :q
              ORDER BY o.id,
                       (o.stock_on_hand > 0) DESC,
                       o.compatible_vehicles_count DESC NULLS LAST
              LIMIT :lim";

        return $this->connection->fetchAllAssociative($sql, $params);
    }
}
