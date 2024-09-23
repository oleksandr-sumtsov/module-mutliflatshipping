<?php
namespace Sumtsov\MultiFlatShipping\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sumtsov\MultiFlatShipping\Model\FlatShippingConfigListProvider;
use Sumtsov\MultiFlatShipping\Model\FlatShippingMethodCountProvider;
use Sumtsov\MultiFlatShipping\Model\CarrierCodeByIndexProvider;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * @covers \Sumtsov\MultiFlatShipping\Model\FlatShippingConfigListProvider
 */
class FlatShippingConfigListProviderTest extends TestCase
{
    private const CONFIG_TEMPLATE = [
        'translate' => 'label',
        'type' => 'text',
        'sortOrder' => 310,
        'showInDefault' => 1,
        'showInWebsite' => 1,
        'showInStore' => 1,
        'label' => 'Flat Rate (Extra)',
        'children' => [
            'active' => [
                'id' => 'active',
                'translate' => 'label',
                'type' => 'select',
                'sortOrder' => 1,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'canRestore' => 1,
                'label' => 'Enabled',
                'source_model' => 'Magento\\Config\\Model\\Config\\Source\\Yesno',
                '_elementType' => 'field',
            ],
            'title' => [
                'id' => 'title',
                'translate' => 'label',
                'type' => 'text',
                'sortOrder' => 2,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'showInStore' => 1,
                'canRestore' => 1,
                'label' => 'Title',
                '_elementType' => 'field',
            ],
            'name' => [
                'id' => 'name',
                'translate' => 'label',
                'type' => 'text',
                'sortOrder' => 3,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'showInStore' => 1,
                'canRestore' => 1,
                'label' => 'Method Name',
                '_elementType' => 'field',
            ],
            'type' => [
                'id' => 'type',
                'translate' => 'label',
                'type' => 'select',
                'sortOrder' => 4,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'canRestore' => 1,
                'label' => 'Type',
                'source_model' => 'Magento\\OfflineShipping\\Model\\Config\\Source\\Flatrate',
                '_elementType' => 'field',
            ],
            'price' => [
                'id' => 'price',
                'translate' => 'label',
                'type' => 'text',
                'sortOrder' => 5,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'canRestore' => 1,
                'label' => 'Price',
                'validate' => 'validate-number validate-zero-or-greater',
                '_elementType' => 'field',
            ],
            'handling_type' => [
                'id' => 'handling_type',
                'translate' => 'label',
                'type' => 'select',
                'sortOrder' => 7,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'canRestore' => 1,
                'label' => 'Calculate Handling Fee',
                'source_model' => 'Magento\\Shipping\\Model\\Source\\HandlingType',
                '_elementType' => 'field',
            ],
            'handling_fee' => [
                'id' => 'handling_fee',
                'translate' => 'label',
                'type' => 'text',
                'sortOrder' => 8,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'label' => 'Handling Fee',
                'validate' => 'validate-number validate-zero-or-greater',
                '_elementType' => 'field',
            ],
            'specificerrmsg' => [
                'id' => 'specificerrmsg',
                'translate' => 'label',
                'type' => 'textarea',
                'sortOrder' => 80,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'showInStore' => 1,
                'canRestore' => 1,
                'label' => 'Displayed Error Message',
                '_elementType' => 'field',
            ],
            'sallowspecific' => [
                'id' => 'sallowspecific',
                'translate' => 'label',
                'type' => 'select',
                'sortOrder' => 90,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'canRestore' => 1,
                'label' => 'Ship to Applicable Countries',
                'frontend_class' => 'shipping-applicable-country',
                'source_model' => 'Magento\\Shipping\\Model\\Config\\Source\\Allspecificcountries',
                '_elementType' => 'field',
            ],
            'specificcountry' => [
                'id' => 'specificcountry',
                'translate' => 'label',
                'type' => 'multiselect',
                'sortOrder' => 91,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'label' => 'Ship to Specific Countries',
                'source_model' => 'Magento\\Directory\\Model\\Config\\Source\\Country',
                'can_be_empty' => 1,
                '_elementType' => 'field',
            ],
            'showmethod' => [
                'id' => 'showmethod',
                'translate' => 'label',
                'type' => 'select',
                'sortOrder' => 92,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'label' => 'Show Method if Not Applicable',
                'source_model' => 'Magento\\Config\\Model\\Config\\Source\\Yesno',
                'frontend_class' => 'shipping-skip-hide',
                '_elementType' => 'field',
            ],
            'sort_order' => [
                'id' => 'sort_order',
                'translate' => 'label',
                'type' => 'text',
                'sortOrder' => 100,
                'showInDefault' => 1,
                'showInWebsite' => 1,
                'label' => 'Sort Order',
                'validate' => 'validate-number validate-zero-or-greater',
                '_elementType' => 'field',
            ],
        ],
        '_elementType' => 'group',
        'path' => 'carriers',
    ];

    /**
     * @var FlatShippingMethodCountProvider|MockObject
     */
    private $flatShippingMethodCountProvider;

    /**
     * @var CarrierCodeByIndexProvider|MockObject
     */
    private $carrierCodeByIndexProvider;

    /**
     * @var array
     */
    private $configTemplate;

    /**
     * @var FlatShippingConfigListProvider
     */
    private $testObject;

    /**
     * Sets up the test environment.
     *
     * This method is called before each test method is executed.
     * It creates mocks for the dependencies and initializes
     * the FlatShippingConfigListProvider instance.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->flatShippingMethodCountProvider = $this->createMock(FlatShippingMethodCountProvider::class);
        $this->carrierCodeByIndexProvider = $this->createMock(CarrierCodeByIndexProvider::class);

        $this->testObject = $objectManager->getObject(
            FlatShippingConfigListProvider::class,
            [
                'flatShippingMethodCountProvider' => $this->flatShippingMethodCountProvider,
                'carrierCodeByIndexProvider' => $this->carrierCodeByIndexProvider,
                'configTemplate' => self::CONFIG_TEMPLATE,
            ]
        );
    }

    /**
     * Data provider for testGet.
     *
     * @return array
     */
    public function dataProviderForTestGet(): array
    {
        $expectedResult = array_merge(self::CONFIG_TEMPLATE, [
            'sortOrder' => 3,
            'label' => 'Flat Rate (Extra) 1',
            'id' => 'flatrate_1',
        ]);
        foreach ($expectedResult['children'] as &$child) {
            $child['path'] = 'carriers/flatrate_1';
        }

        return [
            'Single Method' => [
                'methodCount' => 1,
                'carrierCodes' => ['flatrate_1'],
                'expectedResult' => [
                    'flatrate_1' => $expectedResult
                ]
            ],
//            'Multiple Methods' => [
//                'methodCount' => 2,
//                'carrierCodes' => ['flatrate_1', 'flatrate_2'],
//                'expectedResult' => [
//                    ['template_key' => 'template_value', 'carrier_code' => 'flatrate_1'],
//                    ['template_key' => 'template_value', 'carrier_code' => 'flatrate_2']
//                ]
//            ],
        ];
    }

    /**
     * Tests the get method of FlatShippingConfigListProvider.
     *
     * @dataProvider dataProviderForTestGet
     *
     * @param int   $methodCount
     * @param array $carrierCodes
     * @param array $expectedResult
     *
     * @return void
     */
    public function testGet(int $methodCount, array $carrierCodes, array $expectedResult): void
    {
        // Mock the flatShippingMethodCountProvider to return the method count
        $this->flatShippingMethodCountProvider
            ->expects($this->once())
            ->method('get')
            ->willReturn($methodCount);

        // Mock the carrierCodeByIndexProvider to return the correct carrier codes based on the index
        $this->carrierCodeByIndexProvider
            ->expects($this->exactly($methodCount))
            ->method('get')
            ->willReturnOnConsecutiveCalls(...$carrierCodes);

        // Call the method to be tested
        $result = $this->testObject->get();

        // Assert that the result matches the expected result
        $this->assertEquals($expectedResult, $result);
    }
}

