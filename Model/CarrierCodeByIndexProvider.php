<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Model;

class CarrierCodeByIndexProvider
{
    private const ID_PREFIX = 'flatrate_';

    /**
     * @param int $i
     * @return string
     */
    public function get(int $i): string
    {
        return self::ID_PREFIX . $i;
    }
}
