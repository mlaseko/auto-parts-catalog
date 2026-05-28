<?php

namespace App\Service\Catalog;

class OemNormalizer
{
    /**
     * Strip non-alphanumeric characters and uppercase the query so it can be
     * compared against oitm.canonical_oem_number and
     * oitm_cross_reference.oem_number_normalized.
     */
    public function normalize(string $raw): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $raw));
    }
}
