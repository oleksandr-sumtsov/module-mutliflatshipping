<?php
namespace Sumtsov\MultiFlatShipping\Test\Unit\Plugin\Shipping\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Sumtsov\MultiFlatShipping\Plugin\Shipping\Model\CarrierFactory;
use Magento\Shipping\Model\CarrierFactory as Subject;
use Sumtsov\MultiFlatShipping\Model\Carrier\Flatrate;

/**
 * @covers \Sumtsov\MultiFlatShipping\Plugin\Shipping\Model\CarrierFactory
 */
class CarrierFactoryTest extends TestCase
{
    /**
     * Mock InfiniteFlatRateCarrierCodeSpecification
     *
     * @var \Sumtsov\MultiFlatShipping\Model\InfiniteFlatRateCarrierCodeSpecification|\PHPUnit\Framework\MockObject\MockObject
     */
    private $infiniteFlatRateCarrierCodeSpecification;

    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Object to test
     *
     * @var CarrierFactory
     */
    private $testObject;

    /**
     * Main set up method
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->infiniteFlatRateCarrierCodeSpecification = $this->createMock(\Sumtsov\MultiFlatShipping\Model\InfiniteFlatRateCarrierCodeSpecification::class);
        $this->testObject = $this->objectManager->getObject(
            CarrierFactory::class,
            [
                'infiniteFlatRateCarrierCodeSpecification' => $this->infiniteFlatRateCarrierCodeSpecification,
            ]
        );
    }

    /**
     * Data provider for testAroundCreate.
     *
     * @return array
     */
    public function dataProviderForTestAroundCreate()
    {
        return [
            'Custom Flat Rate Carrier' => [
                'carrierCode' => 'custom_flat_rate',
                'isInfiniteFlatRate' => true,
            ],
            'Standard Carrier' => [
                'carrierCode' => 'flatrate',
                'isInfiniteFlatRate' => false,
            ]
        ];
    }

    /**
     * Test aroundCreate method.
     *
     * @dataProvider dataProviderForTestAroundCreate
     */
    public function testAroundCreate($carrierCode, $isInfiniteFlatRate)
    {
        $subjectMock = $this->createMock(Subject::class);
        $carrierMock = $this->createMock(Flatrate::class);

        // Mock the specification check
        $this->infiniteFlatRateCarrierCodeSpecification
            ->expects($this->exactly(2))
            ->method('isSatisfiedBy')
            ->with($carrierCode)
            ->willReturn($isInfiniteFlatRate);

        // Use a real closure instead of trying to mock it
        $closure = function ($carrierCode) use ($carrierMock) {
            return $carrierMock;
        };

        $result = $this->testObject->aroundCreate($subjectMock, $closure, $carrierCode);

        $this->assertInstanceOf(Flatrate::class, $result);
    }

    /**
     * Data provider for testAroundGet.
     *
     * @return array
     */
    public function dataProviderForTestAroundGet()
    {
        return [
            'Custom Flat Rate Carrier' => [
                'carrierCode' => 'custom_flat_rate',
                'isInfiniteFlatRate' => true,
            ],
            'Standard Carrier' => [
                'carrierCode' => 'flatrate',
                'isInfiniteFlatRate' => false,
            ]
        ];
    }

    /**
     * Test aroundGet method.
     *
     * @dataProvider dataProviderForTestAroundGet
     */
    public function testAroundGet($carrierCode, $isInfiniteFlatRate)
    {
        $subjectMock = $this->createMock(Subject::class);
        $carrierMock = $this->createMock(Flatrate::class);

        // Mock the specification check
        $this->infiniteFlatRateCarrierCodeSpecification
            ->expects($this->exactly(2))
            ->method('isSatisfiedBy')
            ->with($carrierCode)
            ->willReturn($isInfiniteFlatRate);

        // Use a real closure instead of trying to mock it
        $closure = function ($carrierCode) use ($carrierMock) {
            return $carrierMock;
        };

        $result = $this->testObject->aroundGet($subjectMock, $closure, $carrierCode);

        $this->assertInstanceOf(Flatrate::class, $result);
    }
}
