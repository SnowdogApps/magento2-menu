<?php

namespace Snowdog\Menu\Block\Menu\Edit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\NodeTypeProvider;

class Nodes extends Template implements TabInterface
{
    protected $_template = 'menu/nodes.phtml';
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;
    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    public function __construct(
        Template\Context $context,
        NodeRepositoryInterface $nodeRepository,
        NodeTypeProvider $nodeTypeProvider,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
    }

    public function renderNodes()
    {
        $menu = $this->registry->registry('snowmenu_menu');
        $nodes = $this->nodeRepository->getByMenu($menu->getId());
        $data = [];
        foreach ($nodes as $node) {
            $level = $node->getLevel();
            $parent = $node->getParentId() ?: 0;
            if (!isset($data[$level])) {
                $data[$level] = [];
            }
            if (!isset($data[$level][$parent])) {
                $data[$level][$parent] = [];
            }
            $data[$level][$parent][] = $node;
        }

        return $this->renderNodeList(0, null, $data);
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __("Nodes");
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __("Nodes");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }

    private function renderNodeList($level, $parent, $data)
    {
        if (is_null($parent)) {
            $parent = 0;
        }
        if (empty($data[$level])) {
            return;
        }
        if (empty($data[$level][$parent])) {
            return;
        }
        $nodes = $data[$level][$parent];
        $html = '<ul>';
        foreach ($nodes as $node) {
            $html .= '<li class="jstree-open" data-type="' . $node->getType() . '" data-content="' . $node->getContent(
                ) . '" id="node_' . $node->getId() . '"">';
            $html .= $node->getTitle();
            $html .= $this->renderNodeList($level + 1, $node->getId(), $data);
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function getNodeForms()
    {
        return $this->nodeTypeProvider->getEditForms();
    }

    public function getNodeButtons()
    {
        return $this->nodeTypeProvider->getAddButtonLabels();
    }
}