<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Model;

class InfiniteFlatRateCarrierCodeSpecification
{
    /**
     * @param string $carrierCode
     *
     * @return bool
     */
    public function isSatisfiedBy(string $carrierCode): bool
    {
        return strpos($carrierCode, 'flatrate_') === 0;
    }
}
