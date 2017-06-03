<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Snowdog\Menu\Model\NodeType\Category as ModelCategory;

class Category extends AbstractNode
{
    /**
     * @var string
     */
    protected $nodeType = 'category';
    /**
     * @var array
     */
    protected $nodes;
    /**
     * @var array
     */
    protected $categoryUrls;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var string
     */
    protected $_template = 'menu/node_type/category.phtml';
    /**
     * @var ModelCategory
     */
    private $_categoryModel;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ModelCategory $categoryModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ModelCategory $categoryModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->_categoryModel = $categoryModel;
    }

    /**
     * @return \Magento\Catalog\Model\Category|null
     */
    public function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * @return array
     */
    public function getNodeCacheKeyInfo()
    {
        $info = [
            'module_' . $this->getRequest()->getModuleName(),
            'controller_' . $this->getRequest()->getControllerName(),
            'route_' . $this->getRequest()->getRouteName(),
            'action_' . $this->getRequest()->getActionName()
        ];

        $category = $this->getCurrentCategory();
        if ($category) {
            $info[] = 'category_' . $category->getId();
        }

        return $info;
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $data = $this->_categoryModel->fetchConfigData();

        return json_encode($data);
    }

    /**
     * @param array $nodes
     */
    public function fetchData(array $nodes)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        list($this->nodes, $this->categoryUrls) = $this->_categoryModel->fetchData($nodes, $storeId);
    }

    /**
     * @param int $nodeId
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isCurrentCategory(int $nodeId)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException('Invalid node identifier specified');
        }

        $node = $this->nodes[$nodeId];
        $categoryId = (int) $node->getContent();
        $currentCategory = $this->getCurrentCategory();

        return $currentCategory
            ? $currentCategory->getId() == $categoryId
            : false;
    }

    /**
     * @param int $nodeId
     * @param int|null $storeId
     * @return string|false
     * @throws \InvalidArgumentException
     */
    public function getCategoryUrl(int $nodeId, $storeId = null)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException('Invalid node identifier specified');
        }

        $node = $this->nodes[$nodeId];
        $categoryId = (int) $node->getContent();

        if (isset($this->categoryUrls[$categoryId])) {
            $baseUrl = $this->_storeManager->getStore($storeId)->getBaseUrl();
            $categoryUrlPath = $this->categoryUrls[$categoryId];

            return $baseUrl . $categoryUrlPath;
        }

        return false;
    }

    /**
     * @param int $nodeId
     * @param int $level
     * @param int $storeId
     *
     * @return string
     */
    public function getHtml(int $nodeId, int $level, $storeId = null)
    {
        $classes = $level == 0 ? 'level-top' : '';
        $node = $this->nodes[$nodeId];
        $url = $this->getCategoryUrl($nodeId, $storeId);
        $title = $node->getTitle();

        return <<<HTML
<a href="$url" class="$classes" role="menuitem"><span>$title</span></a>
HTML;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getAddButtonLabel()
    {
        return __("Add Category node");
    }
}
