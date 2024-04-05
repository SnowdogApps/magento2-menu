<?php

declare(strict_types=1);

namespace Snowdog\Menu\Ui\Component\Menu\Form\Element;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Exception\LocalizedException;

class Categories implements ArrayInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->retrieveCategories();
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->retrieveCategories(0, false);
    }

    /**
     * Retrieve tree of categories with attributes.
     *
     * @param int $storeId
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
                    'value' => $rootCategory->getId()
                ];
            } else {
                $options[$rootCategory->getId()] = $rootCategory->getName();
            }

            $groupedOptions = [];
            foreach ($collection as $category) {
                if ($toOptionArray) {
                    $groupedOptions[] = [
                        'label' => $category->getName(),
                        'value' => $category->getId()
                    ];
                } else {
                    $options[$category->getId()] = $category->getName();
                }
            }

            if ($toOptionArray && $groupedOptions) {
                $options[] = [
                    'label' => __('Sub categories'),
                    'value' => $groupedOptions
                ];
            }
        }

        return $options;
    }
}
