<?php

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreRepositoryInterface;

class Store
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var array
     */
    private $cachedStores = [];

    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param string|int $storeCode
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @return \Magento\Store\Api\Data\StoreInterface|null
     */
    public function get($storeCode)
    {
        if (isset($this->cachedStores[$storeCode])) {
            return $this->cachedStores[$storeCode];
        }

        try {
            $store = $this->storeRepository->get($storeCode);
            $this->cachedStores[$storeCode] = $store;
        } catch (NoSuchEntityException $exception) {
            $store = null;
        }

        return $store;
    }
}
