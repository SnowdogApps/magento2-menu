<?php

namespace Snowdog\Menu\Block\Menu\Edit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Registry;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Controller\Adminhtml\Menu\Edit;
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
        $menu = $this->registry->registry(Edit::REGISTRY_CODE);
        $data = [];
        if ($menu) {
            $nodes = $this->nodeRepository->getByMenu($menu->getId());
            if (!empty($nodes)) {
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
        }
        return $data;
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
        foreach ($nodes as $node) {
            $menu[] = [
                'type' => $node->getType(),
                'content' => $node->getContent(),
                'classes' => $node->getClasses(),
                'target' => $node->getTarget(),
                'id' => $node->getId(),
                'title' => $node->getTitle(),
                'columns' => $this->renderNodeList($level + 1, $node->getId(), $data) ? $this->renderNodeList($level + 1, $node->getId(), $data) : []
            ];
        }
        return $menu;
    }

    public function getNodeForms()
    {
        return $this->nodeTypeProvider->getEditForms();
    }

    public function getNodeLabels()
    {
        return $this->nodeTypeProvider->getLabels();
    }
}
