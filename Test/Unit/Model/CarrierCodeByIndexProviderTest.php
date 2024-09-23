<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Sumtsov\MultiFlatShipping\Model\CarrierCodeByIndexProvider;

class CarrierCodeByIndexProviderTest extends TestCase
{
    private CarrierCodeByIndexProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new CarrierCodeByIndexProvider();
    }

    /**
     * @dataProvider indexProvider
     */
    public function testGetReturnsCorrectCarrierCode(int $index, string $expectedCode): void
    {
        $this->assertEquals($expectedCode, $this->provider->get($index));
    }

    public function indexProvider(): array
    {
        return [
            'index 0' => [0, 'flatrate_0'],
            'index 1' => [1, 'flatrate_1'],
            'index 10' => [10, 'flatrate_10'],
            'index 12345' => [12345, 'flatrate_12345'],
        ];
    }
}
