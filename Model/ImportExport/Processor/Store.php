<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class Store
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $cachedStores = [];

    public function __construct(StoreRepositoryInterface $storeRepository, StoreManagerInterface $storeManager)
    {
        $this->storeRepository = $storeRepository;
        $this->storeManager = $storeManager;
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

    public function getIdByCode(string $storeCode): ?int
    {
        try {
            $store = $this->storeManager->getStore($storeCode);
            return (int) $store->getId();
        } catch (\Exception $e) {
            return null;
        }
    }
}
