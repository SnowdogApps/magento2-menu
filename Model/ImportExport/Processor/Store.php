<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
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
     */
    public function get($storeCode): ?StoreInterface
    {
        if (isset($this->cachedStores[$storeCode])) {
            return $this->cachedStores[$storeCode];
        }

        try {
            $store = $this->storeRepository->get($storeCode);
        } catch (NoSuchEntityException $exception) {
            $store = null;
        }

        $this->cachedStores[$storeCode] = $store;

        return $store;
    }
}
