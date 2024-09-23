<?php
declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class FlatShippingMethodCountProvider
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
     * @return int
     */
    public function get(): int
    {
        return (int)$this->scopeConfig->getValue('carriers/flatrate_count/count');
    }
}
