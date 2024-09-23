<?php
namespace Sumtsov\MultiFlatShipping\Plugin\Model\Config\Structure;

use Magento\Config\Model\Config\Structure\Data as Subject;
use Sumtsov\MultiFlatShipping\Model\FlatShippingConfigListProvider;

class Data
{
    /**
     * @var FlatShippingConfigListProvider
     */
    private FlatShippingConfigListProvider $flatShippingConfigListProvider;

    public function __construct(FlatShippingConfigListProvider $flatShippingConfigListProvider)
    {
        $this->flatShippingConfigListProvider = $flatShippingConfigListProvider;
    }
    public function afterGet(Subject $subject, $result)
    {
        if (!isset($result['sections']['carriers']['children'] )) {
            return $result;
        }

        $result['sections']['carriers']['children'] = array_merge(
            $result['sections']['carriers']['children'] ,
            $this->flatShippingConfigListProvider->get()
        );

        return $result;
    }
}
