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
    protected $customTemplateFolder = 'menu/custom/custom_url/';

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
        return $this->_customUrlModel->fetchConfigData();
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
        $nodeContent  = $node->getContent();
        $title = $node->getTitle();

        if (!$this->isExternalUrl($nodeContent)) {
            $url = $this->_storeManager->getStore()->getBaseUrl() . $nodeContent;
        } else {
            $url = $nodeContent;
        }

        return <<<HTML
<a href="$url" class="$classes" role="menuitem"><span>$title</span></a>
HTML;
    }

    /**
     * @param string|null $url
     *
     * @return bool
     */
    private function isExternalUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __("Custom Url");
    }
}
