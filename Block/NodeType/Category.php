<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Backend\Block\Template\Context;
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
     * @param Context       $context
     * @param ModelCategory $categoryModel
     * @param array         $data
     */
    public function __construct(
        Context $context,
        ModelCategory $categoryModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryModel = $categoryModel;

        $nameNode = $this->getNodeAttribute(AbstractNode::NAME_CODE);
        $this->addNodeAttribute(
            AbstractNode::NAME_CODE,
            $nameNode->getLabel(),
            'wysiwyg'
        );
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
     * @param int $level
     *
     * @return string
     */
    public function getHtml(int $nodeId, int $level)
    {
        $classes = $level == 0 ? 'level-top' : '';
        $node = $this->nodes[$nodeId];

        if (isset($this->categoryUrls[(int)$node->getContent()])) {
            $url = $this->_storeManager->getStore()->getBaseUrl() . $this->categoryUrls[(int)$node->getContent()];
        } else {
            $url = $this->_storeManager->getStore()->getBaseUrl();
        }

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