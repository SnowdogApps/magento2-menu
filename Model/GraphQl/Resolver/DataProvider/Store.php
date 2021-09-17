<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider;

use Magento\Store\Model\StoreManagerInterface;

class Store
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $stores = [];

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function getCodes(array $storeIds): array
    {
        if (!$this->stores) {
            foreach ($this->storeManager->getStores(true) as $store) {
                $this->stores[$store->getId()] = $store->getCode();
            }
        }

        $storeCodes = [];
        foreach ($storeIds as $storeId) {
            if (isset($this->stores[$storeId])) {
                $storeCodes[$storeId] = $this->stores[$storeId];
            }
        }

        return $storeCodes;
    }
}
