<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model;

use Snowdog\Menu\Api\MenuManagementInterface;
use Magento\Catalog\Model\CategoryManagement;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class MenuManagement implements MenuManagementInterface
{
    /**
     * @var CategoryManagement
     */
    private $categoryManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var array
     */
    private $categoryNames = [];

    /**
     * @param CategoryManagement $categoryManagement
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CategoryManagement $categoryManagement,
        StoreManagerInterface $storeManager,
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryManagement = $categoryManagement;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @param int|null $rootCategoryId
     * @param int|null $depth
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCategoryNodeList($rootCategoryId = null, $depth = null): array
    {
        $categoriesTree = $this->categoryManagement->getTree($rootCategoryId, $depth);
        $categories = $this->generateCategoriesNode($categoriesTree);

        // Preload all category translations
        $this->preloadCategoryTranslations($categories);

        $nodeList = $this->getCategoriesNodeList(0, 0, $categories);

        return $nodeList;
    }

    /**
     * Preload all category translations in one go
     *
     * @param array $categories
     * @return void
     */
    private function preloadCategoryTranslations(array $categories): void
    {
        $categoryIds = [];
        foreach ($categories as $level) {
            foreach ($level as $parentId => $nodes) {
                foreach ($nodes as $node) {
                    $categoryIds[] = $node['entity_id'];
                }
            }
        }

        if (empty($categoryIds)) {
            return;
        }

        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeId = $store->getId();
            if ($storeId === 0) {
                continue; // Skip admin store
            }

            /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
            $collection = $this->categoryCollectionFactory->create();
            $collection->setStoreId($storeId)
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', ['in' => $categoryIds]);

            foreach ($collection as $category) {
                $categoryId = $category->getId();
                if (!isset($this->categoryNames[$categoryId])) {
                    $this->categoryNames[$categoryId] = [];
                }
                $this->categoryNames[$categoryId][$storeId] = $category->getName();
            }
        }
    }

    /**
     * @param Category $node
     * @param array $data
     * @return array
     */
    private function generateCategoriesNode(Category $node, &$data = []): array
    {
        if (!empty($node)) {
            $level = $node->getLevel() - 2;
            $parent = (!$level ? 0 : $node->getParentId());
            if (!isset($data[$level])) {
                $data[$level] = [];
            }
            if (!isset($data[$level][$parent])) {
                $data[$level][$parent] = [];
            }
            $data[$level][$parent][] = $node->getData();

            if ($node->getChildrenData()) {
                foreach ($node->getChildrenData() as $item) {
                    $data = $this->generateCategoriesNode($item, $data);
                }
            }
        }

        return $data;
    }

    /**
     * @param $level
     * @param $parent
     * @param array $data
     * @return array
     */
    private function getCategoriesNodeList($level, $parent, array $data): array
    {
        if ($parent === null) {
            $parent = 0;
        }

        if (empty($data) || empty($data[$level]) || empty($data[$level][$parent])) {
            return [];
        }

        $nodes = $data[$level][$parent];
        $nodeList = [];

        foreach ($nodes as $node) {
            $nodeId = $node['entity_id'];
            $translations = [];

            // Use preloaded translations
            if (isset($this->categoryNames[$nodeId])) {
                foreach ($this->categoryNames[$nodeId] as $storeId => $name) {
                    if ($name !== $node['name']) {
                        $translations[] = [
                            'store_id' => (string)$storeId,
                            'value' => $name
                        ];
                    }
                }
            }

            $nodeList[] = [
                'is_active' => '1',
                'type' => 'category',
                'content' => $nodeId,
                'classes' => '',
                'target' => '0',
                'id' => null,
                'title' => $node['name'],
                'node_template' => null,
                'image' => null,
                'image_alt_text' => null,
                'image_width' => null,
                'image_height' => null,
                'submenu_template' => null,
                'translations' => $translations,
                'columns' => $this->getCategoriesNodeList($level + 1, $nodeId, $data) ?: []
            ];
        }

        return $nodeList;
    }
}
