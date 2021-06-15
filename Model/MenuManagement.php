<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model;

use Snowdog\Menu\Api\MenuManagementInterface;
use Magento\Catalog\Model\CategoryManagement;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class MenuManagement implements MenuManagementInterface
{
    /**
     * @var CategoryManagement
     */
    private $categoryManagement;

    /**
     * @param CategoryManagement $categoryManagement
     */
    public function __construct(
        CategoryManagement $categoryManagement
    ) {
        $this->categoryManagement = $categoryManagement;
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
        $nodeList = $this->getCategoriesNodeList(0, 0, $categories);

        return $nodeList;
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
        foreach ($nodes as $node) {
            $nodeId = $node['id'];
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
                'submenu_template' => null,
                'columns' => $this->getCategoriesNodeList($level + 1, $nodeId, $data) ?: []
            ];
        }

        return $nodeList;
    }
}
