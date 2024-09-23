<?php
namespace Sumtsov\MultiFlatShipping\Plugin\Shipping\Model;

use Magento\Shipping\Model\CarrierFactory as Subject;
use Sumtsov\MultiFlatShipping\Model\InfiniteFlatRateCarrierCodeSpecification;
use Sumtsov\MultiFlatShipping\Api\InfiniteFlatRateInterface;

class CarrierFactory
{
    /**
     * @var InfiniteFlatRateCarrierCodeSpecification
     */
    private InfiniteFlatRateCarrierCodeSpecification $infiniteFlatRateCarrierCodeSpecification;

    public function __construct(InfiniteFlatRateCarrierCodeSpecification $infiniteFlatRateCarrierCodeSpecification)
    {
        $this->infiniteFlatRateCarrierCodeSpecification = $infiniteFlatRateCarrierCodeSpecification;
    }

    public function aroundCreate(Subject $subject, callable $proceed, $carrierCode, $storeId = null)
    {
        return $this->wrapCallBack($proceed, $carrierCode, $storeId);
    }

    public function aroundGet(Subject $subject,  callable $proceed, $carrierCode)
    {
        return $this->wrapCallBack($proceed, $carrierCode);
    }

    private function wrapCallBack($proceed, ...$args)
    {
        $nativeCarrierCode = $args[0];
        if ($this->infiniteFlatRateCarrierCodeSpecification->isSatisfiedBy($nativeCarrierCode)) {
            $args[0] = InfiniteFlatRateInterface::DUMMY_CARRIER;
        }

        $carrier = $proceed(...$args);

        if ($this->infiniteFlatRateCarrierCodeSpecification->isSatisfiedBy($nativeCarrierCode)) {
            $carrier->setId($nativeCarrierCode);
        }

        return $carrier;
    }
}
