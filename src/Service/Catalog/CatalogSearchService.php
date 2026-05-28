<?php

namespace App\Service\Catalog;

use App\Repository\Catalog\OitmRepository;

class CatalogSearchService
{
    public function __construct(
        private readonly OitmRepository $oitmRepository,
        private readonly OemNormalizer $oemNormalizer,
    ) {
    }

    /**
     * Full-text style search across OEM numbers, article numbers, item codes,
     * and cross-reference OEM numbers.
     *
     * @return list<array<string,mixed>>
     */
    public function search(string $query, ?int $vehicleId = null, int $limit = 50): array
    {
        $normalised = $this->oemNormalizer->normalize($query);

        if ($normalised === '') {
            return [];
        }

        return $this->oitmRepository->search($normalised, $vehicleId, $limit);
    }

    /**
     * Browse products by taxonomy level.
     *
     * @return list<array<string,mixed>>
     */
    public function browseByGroupAndComponent(
        string $partGroup,
        string $partComponent,
        ?int $vehicleId = null,
        int $limit = 50,
        int $offset = 0,
    ): array {
        return $this->oitmRepository->findByGroupAndComponent(
            $partGroup,
            $partComponent,
            $vehicleId,
            $limit,
            $offset,
        );
    }
}
