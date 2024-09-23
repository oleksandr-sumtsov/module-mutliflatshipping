<?php

declare(strict_types=1);

namespace Sumtsov\MultiFlatShipping\Model\Config;

use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\Storage\WriterInterface;

class FlatRateCountBackend extends Value
{
    private WriterInterface $configWriter;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @var int
     */
    private int $oldShippingMethodCount;

    /**
     * @return FlatRateCountBackend
     */
    public function beforeSave()
    {
        $this->oldShippingMethodCount = (int)$this->getValue();

        return parent::beforeSave();
    }

    /**
     * Perform actions after config save
     *
     * @return $this
     */
    public function afterSave()
    {
        // Add your custom logic after saving the configuration
        $newShippingMethodCount = $this->getValue();
        for ($i = $newShippingMethodCount; $i <= $this->oldShippingMethodCount; $i++) {
            $this->configWriter->save("carriers/flatrate_$i/active", 0);
        }

        // Example: Clear cache, run custom scripts, etc.
        // $this->_cacheTypeList->cleanType('config');

        return parent::afterSave();
    }
}
