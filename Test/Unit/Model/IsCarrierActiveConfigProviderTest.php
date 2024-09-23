<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Sumtsov\MultiFlatShipping\Model\IsCarrierActiveConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;

class IsCarrierActiveConfigProviderTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var IsCarrierActiveConfigProvider
     */
    private $isCarrierActiveConfigProvider;

    /**
     * Sets up the test environment by creating the necessary mock objects and
     * initializing the class to be tested.
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Create a mock of ScopeConfigInterface
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        // Instantiate IsCarrierActiveConfigProvider with the mocked ScopeConfigInterface
        $this->isCarrierActiveConfigProvider = new IsCarrierActiveConfigProvider($this->scopeConfigMock);
    }

    /**
     * Tests that the get method returns true when the carrier is active.
     *
     * This test verifies that the get method correctly identifies when a carrier
     * is marked as active in the configuration.
     *
     * @return void
     */
    public function testGetReturnsTrueWhenCarrierIsActive(): void
    {
        $carrierCode = 'flatrate';
        $store = 1;

        // Configure the mock to return true for the isSetFlag method call
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with('carriers/' . $carrierCode . '/active', ScopeInterface::SCOPE_STORE, $store)
            ->willReturn(true);

        // Assert that the get method returns true
        $this->assertTrue($this->isCarrierActiveConfigProvider->get($carrierCode, $store));
    }

    /**
     * Tests that the get method returns false when the carrier is inactive.
     *
     * This test verifies that the get method correctly identifies when a carrier
     * is marked as inactive in the configuration.
     *
     * @return void
     */
    public function testGetReturnsFalseWhenCarrierIsInactive(): void
    {
        $carrierCode = 'flatrate';
        $store = 1;

        // Configure the mock to return false for the isSetFlag method call
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with('carriers/' . $carrierCode . '/active', ScopeInterface::SCOPE_STORE, $store)
            ->willReturn(false);

        // Assert that the get method returns false
        $this->assertFalse($this->isCarrierActiveConfigProvider->get($carrierCode, $store));
    }
}
