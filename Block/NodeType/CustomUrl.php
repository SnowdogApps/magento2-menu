<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\View\Element\Template\Context;
use Snowdog\Menu\Model\NodeType\CustomUrl as CustomUrlModel;

class CustomUrl extends AbstractNode
{
    /**
     * @var string
     */
    protected $nodeType = 'custom_url';
    /**
     * @var array
     */
    protected $nodes;
    /**
     * @var string
     */
    protected $_template = 'menu/node_type/custom_url.phtml';
    /**
     * @var CustomUrlModel
     */
    private $_customUrlModel;

    /**
     * CustomUrl constructor.
     *
     * @param Context $context
     * @param CustomUrlModel $customUrlModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomUrlModel $customUrlModel,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customUrlModel = $customUrlModel;
    }

    /**
     * @inheritDoc
     */
    public function getJsonConfig()
    {
        $data = [
            "snowMenuSimpleField" => [
                "type" => "custom_url"
            ]
        ];
        return json_encode($data);
    }

    /**
     * @param array $nodes
     */
    public function fetchData(array $nodes)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        $this->nodes = $this->_customUrlModel->fetchData($nodes, $storeId);
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
        $url = $this->_storeManager->getStore()->getBaseUrl() . $node->getContent();
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
        return __("Add Custom Url node");
    }
}
