<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class IsCarrierActiveConfigProvider
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $carrierCode
     * @param $store
     *
     * @return bool
     */
    public function get(string $carrierCode, $store): bool
    {
        return $this->scopeConfig->isSetFlag(
            'carriers/' . $carrierCode . '/active',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
