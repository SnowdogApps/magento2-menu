<?php

namespace Snowdog\Menu\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class Stores implements OptionSourceInterface
{
    private $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function toOptionArray()
    {
        $values = [];
        foreach ($this->storeManager->getStores(false) as $storeId => $store) {
            $values[] = [
                'label' => $store->getName(),
                'value' => $storeId,
            ];
        }

        return $values;
    }
}
