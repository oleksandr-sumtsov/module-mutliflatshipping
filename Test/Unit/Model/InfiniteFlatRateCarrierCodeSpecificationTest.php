<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sumtsov\MultiFlatShipping\Model\InfiniteFlatRateCarrierCodeSpecification;

class InfiniteFlatRateCarrierCodeSpecificationTest extends TestCase
{
    /**
     * @var InfiniteFlatRateCarrierCodeSpecification
     */
    private $specification;

    protected function setUp(): void
    {
        $this->specification = new InfiniteFlatRateCarrierCodeSpecification();
    }

    public function testIsSatisfiedByReturnsTrueForValidCarrierCode()
    {
        $carrierCode = 'flatrate_1';
        $result = $this->specification->isSatisfiedBy($carrierCode);

        $this->assertTrue($result, 'Expected true for carrier code starting with "flatrate_"');
    }

    public function testIsSatisfiedByReturnsFalseForInvalidCarrierCode()
    {
        $carrierCode = 'notflatrate_1';
        $result = $this->specification->isSatisfiedBy($carrierCode);

        $this->assertFalse($result, 'Expected false for carrier code not starting with "flatrate_"');
    }

    public function testIsSatisfiedByReturnsFalseForEmptyCarrierCode()
    {
        $carrierCode = '';
        $result = $this->specification->isSatisfiedBy($carrierCode);

        $this->assertFalse($result, 'Expected false for empty carrier code');
    }
}

