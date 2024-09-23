<?php

use PHPUnit\Framework\TestCase;
use Sumtsov\MultiFlatShipping\Plugin\Model\Config\Structure\Data;
use Sumtsov\MultiFlatShipping\Model\FlatShippingConfigListProvider;
use Magento\Config\Model\Config\Structure\Data as Subject;

class DataTest extends TestCase
{
    /**
     * @var FlatShippingConfigListProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $flatShippingConfigListProviderMock;

    /**
     * @var Data
     */
    private $plugin;

    /**
     * Set up the test environment.
     *
     * This method is executed before each test method.
     * It creates the necessary mock objects and initializes the plugin class.
     */
    protected function setUp(): void
    {
        $this->flatShippingConfigListProviderMock = $this->createMock(FlatShippingConfigListProvider::class);
        $this->plugin = new Data($this->flatShippingConfigListProviderMock);
    }

    /**
     * Test the afterGet method when the carriers section is not present.
     *
     * This test verifies that the afterGet method returns the result unmodified
     * when the 'carriers' section is not present in the configuration array.
     */
    public function testAfterGetWithoutCarriersSection()
    {
        $subjectMock = $this->createMock(Subject::class);
        $result = [
            'sections' => [
                'general' => [
                    'id' => 'general',
                ],
            ],
        ];

        $actualResult = $this->plugin->afterGet($subjectMock, $result);

        $this->assertSame($result, $actualResult, 'Result should not be modified when carriers section is missing');
    }

    /**
     * Test the afterGet method when the carriers section is present.
     *
     * This test verifies that the afterGet method merges the existing children
     * in the 'carriers' section with the additional configuration provided by
     * FlatShippingConfigListProvider.
     */
    public function testAfterGetWithCarriersSection()
    {
        $subjectMock = $this->createMock(Subject::class);
        $flatShippingConfigList = [
            'custom_flat_rate' => [
                'id' => 'custom_flat_rate',
            ],
        ];
        $result = [
            'sections' => [
                'carriers' => [
                    'children' => [
                        'flatrate' => [
                            'id' => 'flatrate',
                        ],
                    ],
                ],
            ],
        ];

        $this->flatShippingConfigListProviderMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($flatShippingConfigList);

        $expectedResult = [
            'sections' => [
                'carriers' => [
                    'children' => array_merge(
                        $result['sections']['carriers']['children'],
                        $flatShippingConfigList
                    ),
                ],
            ],
        ];

        $actualResult = $this->plugin->afterGet($subjectMock, $result);

        $this->assertSame($expectedResult, $actualResult, 'Result should be modified with additional flat shipping configs');
    }
}
