<?php

declare(strict_types=1);

namespace Snowdog\Menu\Ui\Component\Menu\Form\Element;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class Categories implements ArrayInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $stores = $this->storeManager->getStores(true);

        foreach ($stores as $store) {
            $categories = $this->retrieveCategories((int) $store->getId());
            $options = array_merge($options, $categories);
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        $options = [];
        $stores = $this->storeManager->getStores(true);

        foreach ($stores as $store) {
            $categories = $this->retrieveCategories((int) $store->getId(), false);
            $options = array_merge($options, $categories);
        }

        return $options;
    }

    /**
     * Retrieve tree of categories with attributes.
     *
     * @param int $storeId
     * @param bool $toOptionArray
     * @return array
     * @throws LocalizedException
     */
    private function retrieveCategories(int $storeId = 0, $toOptionArray = true): array
    {
        /* @var $collection Collection */
        $collection = $this->categoryCollectionFactory->create();

        $collection->addAttributeToSelect(['name', 'is_active', 'parent_id'])
            ->addFieldToFilter('level', 1)
            ->setStoreId($storeId);

        $options = [];

        foreach ($collection as $rootCategory) {
            /* @var $collection Collection */
            $collection = $this->categoryCollectionFactory->create();

            $collection->addAttributeToSelect(['name', 'is_active', 'parent_id'])
                ->addFieldToFilter('level', 2)
                ->addFieldToFilter('path', ['like' => '1/'.$rootCategory->getId().'/%'])
                ->setStoreId($storeId);

            if ($toOptionArray) {
                $options[] = [
                    'label' => $rootCategory->getName(),
                    'value' => $rootCategory->getId(),
                    'store_id' => (string) $storeId,
                ];
            } else {
                $options[$rootCategory->getId()] = $rootCategory->getName();
            }

            $groupedOptions = [];
            foreach ($collection as $category) {
                if ($toOptionArray) {
                    $groupedOptions[] = [
                        'label' => $category->getName(),
                        'value' => $category->getId(),
                        'store_id' => (string) $storeId,
                    ];
                } else {
                    $options[$category->getId()] = $category->getName();
                }
            }

            if ($toOptionArray && $groupedOptions) {
                $options[] = [
                    'label' => __('Sub categories'),
                    'value' => $groupedOptions,
                    'store_id' => (string) $storeId,
                ];
            }
        }

        return $options;
    }
}
