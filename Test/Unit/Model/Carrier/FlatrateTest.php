<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Test\Unit\Model\Carrier;

use PHPUnit\Framework\TestCase;
use Sumtsov\MultiFlatShipping\Model\Carrier\Flatrate;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator;
use Magento\Shipping\Model\Rate\Result;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Store\Model\ScopeInterface;
use ReflectionClass;

class FlatrateTest extends TestCase
{
    private const CARRIER_CODE = 'flatrate_1';

    /** @var ScopeConfigInterface|MockObject */
    private $scopeConfigMock;

    /** @var ErrorFactory|MockObject */
    private $rateErrorFactoryMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var ResultFactory|MockObject */
    private $rateResultFactoryMock;

    /** @var MethodFactory|MockObject */
    private $rateMethodFactoryMock;

    /** @var ItemPriceCalculator|MockObject */
    private $itemPriceCalculatorMock;

    /** @var RateRequest|MockObject */
    private $rateRequestMock;

    /** @var Flatrate */
    private $flatrate;

    /**
     * Sets up the test environment before each test method.
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->rateErrorFactoryMock = $this->createMock(ErrorFactory::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->rateResultFactoryMock = $this->createMock(ResultFactory::class);
        $this->rateMethodFactoryMock = $this->createMock(MethodFactory::class);
        $this->rateMethodMock =
        $this->itemPriceCalculatorMock = $this->createMock(ItemPriceCalculator::class);
        $this->rateRequestMock = $this->createMock(RateRequest::class);

        $this->flatrate = new Flatrate(
            $this->scopeConfigMock,
            $this->rateErrorFactoryMock,
            $this->loggerMock,
            $this->rateResultFactoryMock,
            $this->rateMethodFactoryMock,
            $this->itemPriceCalculatorMock
        );
    }

    /**
     * Tests that the setId method sets the carrier code.
     */
    public function testSetIdSetsCarrierCode(): void
    {
        $this->flatrate->setId(self::CARRIER_CODE);

        $reflection = new ReflectionClass($this->flatrate);
        $property = $reflection->getProperty('_code');
        $property->setAccessible(true);

        $this->assertEquals(self::CARRIER_CODE, $property->getValue($this->flatrate));
        $this->assertEquals(self::CARRIER_CODE, $this->flatrate->getId());
    }

    /**
     * Tests that collectRates returns false when the carrier is inactive.
     */
    public function testCollectRatesForDisabledShippingMethod(): void
    {
        $this->scopeConfigMock->method('isSetFlag')
            ->willReturnMap([
                ['carriers/' . self::CARRIER_CODE . '/c', ScopeInterface::SCOPE_STORE, null, false],
            ]);
        $this->assertFalse($this->flatrate->collectRates($this->rateRequestMock));
    }

    /**
     * Tests that collectRates returns a Result object with a shipping price when the carrier is active.
     */
    public function testCollectRatesReturnsFalseWhenCarrierIsActivePerOrder(): void
    {
        $this->scopeConfigMock->method('isSetFlag')
            ->willReturnMap([
                ['carriers/flatrate/c', ScopeInterface::SCOPE_STORE, null, true],
            ]);

        $this->scopeConfigMock->method('getValue')->willReturnMap(
            [
                ['carriers/flatrate/price', ScopeInterface::SCOPE_STORE, null, '10.00'],
                ['carriers/flatrate/type', ScopeInterface::SCOPE_STORE, null, 'O'],
            ]
        );

        $this->itemPriceCalculatorMock->method('getShippingPricePerOrder')
            ->willReturn(10.00);

        $resultMock = $this->createMock(Result::class);
        $this->rateResultFactoryMock->method('create')
            ->willReturn($resultMock);

        $methodMock = $this->createMock(Method::class);
        $this->rateMethodFactoryMock->method('create')
            ->willReturn($methodMock);

        $resultMock->expects($this->once())
            ->method('append')
            ->with($methodMock);

        $this->flatrate->collectRates($this->rateRequestMock);
    }

    /**
     * Tests that collectRates returns a Result object with a shipping price when the carrier is active.
     */
    public function testCollectRatesReturnsFalseWhenCarrierIsActivePerItem(): void
    {
        $this->scopeConfigMock->method('isSetFlag')
            ->willReturnMap([
                ['carriers/flatrate/c', ScopeInterface::SCOPE_STORE, null, true],
            ]);

        $this->scopeConfigMock->method('getValue')->willReturnMap(
            [
                ['carriers/flatrate/price', ScopeInterface::SCOPE_STORE, null, '10.00'],
                ['carriers/flatrate/type', ScopeInterface::SCOPE_STORE, null, 'I'],
            ]
        );

        $this->itemPriceCalculatorMock->method('getShippingPricePerItem')
            ->willReturn(10.00);

        $resultMock = $this->createMock(Result::class);
        $this->rateResultFactoryMock->method('create')
            ->willReturn($resultMock);

        $methodMock = $this->createMock(Method::class);
        $this->rateMethodFactoryMock->method('create')
            ->willReturn($methodMock);

        $resultMock->expects($this->once())
            ->method('append')
            ->with($methodMock);

        $this->flatrate->collectRates($this->rateRequestMock);
    }
}
