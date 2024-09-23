<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Tests\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Sumtsov\MultiFlatShipping\Model\FlatShippingMethodCountProvider;

class FlatShippingMethodCountProviderTest extends TestCase
{
    /**
     * @var FlatShippingMethodCountProvider
     */
    private $provider;

    /**
     * @var ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $scopeConfigMock;

    /**
     * Sets up the test environment.
     *
     * This method is called before each test method is executed.
     * It creates a mock for the ScopeConfigInterface and initializes
     * the FlatShippingMethodCountProvider instance with the mock.
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->provider = new FlatShippingMethodCountProvider($this->scopeConfigMock);
    }

    /**
     * Tests that the `get` method returns the expected count as an integer.
     *
     * This test verifies that when the configuration value is a valid integer
     * string, the `get` method correctly converts it to an integer and returns it.
     */
    public function testGetReturnsCountAsInt()
    {
        $expectedCount = 5;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('carriers/flatrate_count/count')
            ->willReturn((string)$expectedCount);

        $result = $this->provider->get();

        $this->assertSame($expectedCount, $result, 'Expected count to match the value from the config.');
    }

    /**
     * Tests that the `get` method returns 0 when the configuration value is null.
     *
     * This test checks that if the configuration value is null, the `get` method
     * correctly handles this case and returns 0.
     */
    public function testGetReturnsZeroWhenConfigValueIsNull()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('carriers/flatrate_count/count')
            ->willReturn(null);

        $result = $this->provider->get();

        $this->assertSame(0, $result, 'Expected count to be 0 when config value is null.');
    }

    /**
     * Tests that the `get` method returns 0 when the configuration value is non-numeric.
     *
     * This test verifies that if the configuration value is a non-numeric string,
     * the `get` method correctly handles this case and returns 0.
     */
    public function testGetReturnsZeroWhenConfigValueIsNonNumeric()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('carriers/flatrate_count/count')
            ->willReturn('non-numeric');

        $result = $this->provider->get();

        $this->assertSame(0, $result, 'Expected count to be 0 when config value is non-numeric.');
    }
}

