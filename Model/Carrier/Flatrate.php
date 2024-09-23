<?php

namespace Sumtsov\MultiFlatShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator;
use Magento\OfflineShipping\Model\Carrier\Flatrate as BaseFlatrate;

class Flatrate extends BaseFlatrate
{
    /**
     * @var ItemPriceCalculator
     */
    private $itemPriceCalculator;

    /**
     * Flatrate constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param ItemPriceCalculator $itemPriceCalculator
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        ItemPriceCalculator $itemPriceCalculator,
        array $data = []
    ) {
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $rateResultFactory,
            $rateMethodFactory,
            $itemPriceCalculator,
            $data
        );

        $this->itemPriceCalculator = $itemPriceCalculator;
    }

    /**
     * Set the carrier ID.
     *
     * @param string $carrierCode
     *
     * @return $this
     */
    public function setId($carrierCode)
    {
        $this->_code = $carrierCode;

        return parent::setId($carrierCode);
    }

    /**
     * Get the shipping price based on the request and free boxes count.
     *
     * @param RateRequest $request
     * @param int $freeBoxes
     *
     * @return float|string|false
     */
    private function getShippingPrice(RateRequest $request, $freeBoxes)
    {
        $shippingPrice = false;

        $configPrice = $this->getConfigData('price');
        if ($this->getConfigData('type') === 'O') {
            // per order
            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerOrder($request, $configPrice, $freeBoxes);
        } elseif ($this->getConfigData('type') === 'I') {
            // per item
            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerItem($request, $configPrice, $freeBoxes);
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false && $request->getPackageQty() == $freeBoxes) {
            $shippingPrice = '0.00';
        }

        return $shippingPrice;
    }

    /**
     * Collect shipping rates for the carrier based on the request.
     *
     * @param RateRequest $request
     *
     * @return Result|false
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('c')) {
            return false;
        }

        $freeBoxes = $this->getFreeBoxesCount($request);
        $this->setFreeBoxes($freeBoxes);

        /** @var Result $result */
        $result = $this->_rateResultFactory->create();

        $shippingPrice = $this->getShippingPrice($request, $freeBoxes);

        if ($shippingPrice !== false) {
            $method = $this->createResultMethod($shippingPrice);
            $result->append($method);
        }

        return $result;
    }

    /**
     * Create a result method with the given shipping price.
     *
     * @param float|string $shippingPrice
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function createResultMethod($shippingPrice)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);

        return $method;
    }
}
