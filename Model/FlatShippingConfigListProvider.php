<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Model;

class FlatShippingConfigListProvider
{
    /**
     * @var CarrierCodeByIndexProvider
     */
    private CarrierCodeByIndexProvider $carrierCodeByIndexProvider;

    /**
     * @var array
     */
    private array $configTemplate;

    /**
     * @var FlatShippingMethodCountProvider
     */
    private FlatShippingMethodCountProvider $flatShippingMethodCountProvider;

    /**
     * @param FlatShippingMethodCountProvider $flatShippingMethodCountCountProvider
     * @param CarrierCodeByIndexProvider $carrierCodeByIndexProvider
     * @param array $configTemplate
     */
    public function __construct(
        FlatShippingMethodCountProvider $flatShippingMethodCountCountProvider,
        CarrierCodeByIndexProvider $carrierCodeByIndexProvider,
        array $configTemplate
    ) {
        $this->configTemplate = $configTemplate;
        $this->carrierCodeByIndexProvider = $carrierCodeByIndexProvider;
        $this->flatShippingMethodCountProvider = $flatShippingMethodCountCountProvider;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $configList = [];
        $count = $this->flatShippingMethodCountProvider->get();
        for ($i = 1; $i <= $count; $i++) {
            $preparedConfigItem = $this->configTemplate;
            $carrierId = $this->carrierCodeByIndexProvider->get($i);
            $preparedConfigItem['id'] = $carrierId;
            $preparedConfigItem['label'] .= " " . $i;
            $preparedConfigItem['sortOrder'] = ($i + 2);
            foreach ($preparedConfigItem['children'] as &$childItem) {
                $childItem['path'] = 'carriers/' . $carrierId;
            }
            $configList['flatrate_' . $i] = $preparedConfigItem;
        }

        return $configList;
    }
}
