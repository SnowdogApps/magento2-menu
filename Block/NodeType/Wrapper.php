<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\Phrase;
use Snowdog\Menu\Model\TemplateResolver;
use Magento\Framework\View\Element\Template\Context;
use Snowdog\Menu\Model\NodeType\Wrapper as WrapperModel;

class Wrapper extends AbstractNode
{
    /**
     * @var string
     */
    protected $defaultTemplate = 'menu/node_type/wrapper.phtml';

    /**
     * @var string
     */
    protected $customTemplateFolder = 'menu/custom/wrapper/';

    /**
     * @var string
     */
    protected $nodeType = 'wrapper';

    /**
     * @var array
     */
    protected $nodes;

    /**
     * @var WrapperModel
     */
    private $wrapperModel;

    public function __construct(
        Context $context,
        WrapperModel $wrapperModel,
        TemplateResolver $templateResolver,
        $data = []
    ) {
        $this->wrapperModel = $wrapperModel;

        parent::__construct($context, $templateResolver, $data);
    }

    /**
     * @inheritDoc
     */
    public function getJsonConfig()
    {
        return $this->wrapperModel->fetchConfigData();
    }

    /**
     * @param array $nodes
     */
    public function fetchData(array $nodes)
    {
        $this->nodes = $this->wrapperModel->fetchData(
            $nodes,
            $this->_storeManager->getStore()->getId()
        );
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
        $nodeClass = $node->getClasses();

        return <<<HTML
<div class="$classes $nodeClass" role="menuitem"></div>
HTML;
    }

    /**
     * @return Phrase
     */
    public function getLabel()
    {
        return __("Wrapper");
    }
}
