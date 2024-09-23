<?php

namespace Sumtsov\MultiFlatShipping\Plugin\Shipping\Model;

use Magento\Shipping\Model\Config as Subject;
use Sumtsov\MultiFlatShipping\Api\InfiniteFlatRateInterface;
use Magento\Shipping\Model\CarrierFactory;
use Sumtsov\MultiFlatShipping\Model\CarrierCodeByIndexProvider;
use Sumtsov\MultiFlatShipping\Model\FlatShippingMethodCountProvider;
use Sumtsov\MultiFlatShipping\Model\IsCarrierActiveConfigProvider;

class Config
{
    /**
     * @var CarrierCodeByIndexProvider
     */
    private CarrierCodeByIndexProvider $carrierCodeByIndexProvider;

    /**
     * @var CarrierFactory
     */
    private CarrierFactory $carrierFactory;

    /**
     * @var IsCarrierActiveConfigProvider
     */
    private IsCarrierActiveConfigProvider $isCarrierActiveConfigProvider;

    /**
     * @var FlatShippingMethodCountProvider
     */
    private FlatShippingMethodCountProvider $flatShippingMethodCountProvider;

    public function __construct(
        CarrierFactory $carrierFactory,
        CarrierCodeByIndexProvider $carrierCodeByIndexProvider,
        FlatShippingMethodCountProvider $flatShippingMethodCountProvider,
        IsCarrierActiveConfigProvider $isCarrierActiveConfigProvider
    ) {
        $this->carrierFactory = $carrierFactory;
        $this->carrierCodeByIndexProvider = $carrierCodeByIndexProvider;
        $this->flatShippingMethodCountProvider = $flatShippingMethodCountProvider;
        $this->isCarrierActiveConfigProvider = $isCarrierActiveConfigProvider;
    }

    public function afterGetActiveCarriers(Subject $subject, $result, $store = null)
    {
        $flatShippingMethodCount = $this->flatShippingMethodCountProvider->get();
        for ($i = 1; $i <= $flatShippingMethodCount; $i++) {
            $carrierCode = $this->carrierCodeByIndexProvider->get($i);
            if (!$this->isCarrierActiveConfigProvider->get($carrierCode, $store)) {
                continue;
            }
            $carrierModel = $this->carrierFactory->create(InfiniteFlatRateInterface::DUMMY_CARRIER, $store);
            $carrierModel->setId($carrierCode);
            $result[$carrierCode] = $carrierModel;
        }

        return $result;
    }
}
