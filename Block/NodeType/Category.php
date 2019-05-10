<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Snowdog\Menu\Model\TemplateResolver;
use Snowdog\Menu\Model\NodeType\Category as ModelCategory;

class Category extends AbstractNode
{
    /**
     * @var string
     */
    protected $defaultTemplate = 'menu/node_type/category.phtml';

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
     * @var array
     */
    protected $categories;
    /**
     * @var Registry
     */
    private $coreRegistry;

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
     * @param TemplateResolver $templateResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ModelCategory $categoryModel,
        TemplateResolver $templateResolver,
        CategoryRepository $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $templateResolver, $data);
        $this->coreRegistry = $coreRegistry;
        $this->_categoryModel = $categoryModel;

        $this->categoryRepository = $categoryRepository;
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

        return $data;
    }

    /**
     * @param array $nodes
     */
    public function fetchData(array $nodes)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        list($this->nodes, $this->categoryUrls, $this->categories) = $this->_categoryModel->fetchData($nodes, $storeId);
    }

    /**
     * @param int $nodeId
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isCurrentCategory($nodeId)
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
    public function getCategoryUrl($nodeId, $storeId = null)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException('Invalid node identifier specified');
        }

        $node = $this->nodes[$nodeId];
        $categoryId = (int) $node->getContent();

        if (isset($this->categories[$categoryId])) {
            return $this->categories[$categoryId]->getUrl();
        } else {
            return false;
        }
    }

    /**
     * @param int $nodeId
     * @param int $level
     * @param int $storeId
     *
     * @return string
     */
    public function getHtml($nodeId, $level, $storeId = null)
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
    public function getLabel()
    {
        return __("Category");
    }

    /**
     * Get the category associated with the menu node.
     *
     * @param $nodeId
     * @param null $storeId
     * @return \Magento\Catalog\Api\Data\CategoryInterface|mixed
     */
    public function getCategory($nodeId, $storeId = null)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException("The menu node ($nodeId) does not exist.");
        }

        /* @var \Snowdog\Menu\Model\Menu\Node $node */
        $node = $this->nodes[$nodeId];

        $categoryId = (int) $node->getContent();

        if (!isset($this->categories[$categoryId])) {
            throw new \InvalidArgumentException("Category $categoryId defined for menu node \"{$node->getTitle()}\" ($nodeId) does not exist.");
        }

        /* @var \Magento\Catalog\Model\Category $category */
        $category = $this->categories[$categoryId];

        return $category;
    }
}
