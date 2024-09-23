<?php

namespace Sumtsov\MultiFlatShipping\Test\Unit\Plugin\Shipping\Model;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sumtsov\MultiFlatShipping\Plugin\Shipping\Model\Config;
use Magento\Shipping\Model\Config as Subject;
use Sumtsov\MultiFlatShipping\Api\InfiniteFlatRateInterface;
use Magento\Shipping\Model\CarrierFactory;
use Sumtsov\MultiFlatShipping\Model\CarrierCodeByIndexProvider;
use Sumtsov\MultiFlatShipping\Model\FlatShippingMethodCountProvider;
use Sumtsov\MultiFlatShipping\Model\IsCarrierActiveConfigProvider;
use Sumtsov\MultiFlatShipping\Model\Carrier\Flatrate;

class ConfigTest extends TestCase
{
    /**
     * @var CarrierFactory|MockObject
     */
    private $carrierFactoryMock;

    /**
     * @var CarrierCodeByIndexProvider|MockObject
     */
    private $carrierCodeByIndexProviderMock;

    /**
     * @var IsCarrierActiveConfigProvider|MockObject
     */
    private $isCarrierActiveConfigProviderMock;

    /**
     * @var FlatShippingMethodCountProvider|MockObject
     */
    private $flatShippingMethodCountProviderMock;

    /**
     * @var Config
     */
    private $plugin;

    protected function setUp(): void
    {
        $this->carrierFactoryMock = $this->createMock(CarrierFactory::class);
        $this->carrierCodeByIndexProviderMock = $this->createMock(CarrierCodeByIndexProvider::class);
        $this->flatShippingMethodCountProviderMock = $this->createMock(FlatShippingMethodCountProvider::class);
        $this->isCarrierActiveConfigProviderMock = $this->createMock(IsCarrierActiveConfigProvider::class);

        $this->plugin = new Config(
            $this->carrierFactoryMock,
            $this->carrierCodeByIndexProviderMock,
            $this->flatShippingMethodCountProviderMock,
            $this->isCarrierActiveConfigProviderMock
        );
    }

    public function testAfterGetActiveCarriersWithNoActiveCarriers()
    {
        $subjectMock = $this->createMock(Subject::class);
        $storeMock = 'store_code';
        $result = ['existing_carrier' => 'existing_carrier_model'];

        $this->flatShippingMethodCountProviderMock->expects($this->once())
            ->method('get')
            ->willReturn(2);

        $this->carrierCodeByIndexProviderMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([1], [2])
            ->willReturnOnConsecutiveCalls('carrier_code_1', 'carrier_code_2');

        $this->isCarrierActiveConfigProviderMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['carrier_code_1', $storeMock], ['carrier_code_2', $storeMock])
            ->willReturn(false);

        $actualResult = $this->plugin->afterGetActiveCarriers($subjectMock, $result, $storeMock);

        $this->assertSame($result, $actualResult);
    }

    public function testAfterGetActiveCarriersWithActiveCarriers()
    {
        $subjectMock = $this->createMock(Subject::class);
        $storeMock = 'store_code';
        $result = ['existing_carrier' => 'existing_carrier_model'];

        $this->flatShippingMethodCountProviderMock->expects($this->once())
            ->method('get')
            ->willReturn(2);

        $this->carrierCodeByIndexProviderMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([1], [2])
            ->willReturnOnConsecutiveCalls('carrier_code_1', 'carrier_code_2');

        $this->isCarrierActiveConfigProviderMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['carrier_code_1', $storeMock], ['carrier_code_2', $storeMock])
            ->willReturnOnConsecutiveCalls(true, false);

        $carrierMock = $this->createMock(Flatrate::class);
        $carrierMock->expects($this->once())
            ->method('setId')
            ->with('carrier_code_1');

        $this->carrierFactoryMock->expects($this->once())
            ->method('create')
            ->with(InfiniteFlatRateInterface::DUMMY_CARRIER, $storeMock)
            ->willReturn($carrierMock);

        $expectedResult = array_merge($result, ['carrier_code_1' => $carrierMock]);

        $actualResult = $this->plugin->afterGetActiveCarriers($subjectMock, $result, $storeMock);

        $this->assertSame($expectedResult, $actualResult);
    }
}
