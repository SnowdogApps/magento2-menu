<?php

namespace Snowdog\Menu\Block\Menu\Edit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Registry;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Block\Element\Editor;
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
    /**
     * @var Config
     */
    private $wysiwygConfig;
    /**
     * @var Editor
     */
    private $editor;

    public function __construct(
        Template\Context $context,
        NodeRepositoryInterface $nodeRepository,
        NodeTypeProvider $nodeTypeProvider,
        Registry $registry,
        Config $wysiwygConfig,
        Editor $editor,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->editor = $editor;
        $config = $this->wysiwygConfig->getConfig([
            'add_variables' => false,
            'add_widgets' => false,
            'add_images' => false,
            'height' => '100px',
            'hidden' => true
        ]);
        $this->editor->setConfig($config);
    }

    public function renderNodes()
    {
        $menu = $this->registry->registry(Edit::REGISTRY_CODE);
        if ($menu) {
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
        return '';
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
            $html .= '<li class="jstree-close" data-type="' . $node->getType() . '" data-content="' . $node->getContent(
                ) . '" data-classes="' . $node->getClasses() . '" id="node_' . $node->getId() . '"">';
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

    public function getEditor($id, $name)
    {
        $this->editor->setId($id);
        $this->editor->setName($name);
        return $this->editor;
    }
}