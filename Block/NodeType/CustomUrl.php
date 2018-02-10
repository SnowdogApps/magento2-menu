<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\View\Element\Template\Context;
use Snowdog\Menu\Model\TemplateResolver;
use Snowdog\Menu\Model\NodeType\CustomUrl as CustomUrlModel;

class CustomUrl extends AbstractNode
{
    const NAME_TARGET = 'node_target';

    /**
     * @var string
     */
    protected $defaultTemplate = 'menu/node_type/custom_url.phtml';

    /**
     * @var string
     */
    protected $nodeType = 'custom_url';
    /**
     * @var array
     */
    protected $nodes;

    /**
     * @var CustomUrlModel
     */
    private $_customUrlModel;

    /**
     * CustomUrl constructor.
     *
     * @param Context $context
     * @param CustomUrlModel $customUrlModel
     * @param TemplateResolver $templateResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomUrlModel $customUrlModel,
        TemplateResolver $templateResolver,
        $data = []
    ) {
        parent::__construct($context, $templateResolver, $data);
        $this->addNodeAttribute(self::NAME_TARGET, 'Node target blank', 'checkbox');
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
        return $data;
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
    public function getHtml($nodeId, $level)
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
    public function getLabel()
    {
        return __("Custom Url");
    }
}
