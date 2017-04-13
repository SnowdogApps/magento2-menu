<?php

namespace Snowdog\Menu\Block;

use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaFactory;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\NodeTypeProvider;

class Menu extends Template implements IdentityInterface
{
    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;
    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;
    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    private $nodes;
    private $menu;
    /**
     * @var SearchCriteriaFactory
     */
    private $searchCriteriaFactory;
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var string
     */
    private $submenuTemplate = 'menu/sub_menu.phtml';

    public function __construct(
        Template\Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        NodeTypeProvider $nodeTypeProvider,
        SearchCriteriaFactory $searchCriteriaFactory,
        FilterGroupBuilder $filterGroupBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [\Snowdog\Menu\Model\Menu::CACHE_TAG, Block::CACHE_TAG];
    }

    protected function getCacheLifetime()
    {
        return 60*60*24*365;
    }

    /**
     * @return \Snowdog\Menu\Model\Menu
     */
    public function getMenu()
    {
        if(!$this->menu) {
            $storeId = $this->_storeManager->getStore()->getId();
            $this->menu = $this->menuRepository->get($this->getData('menu'), $storeId);
        }
        return $this->menu;
    }


    public function getCacheKeyInfo()
    {
        $parentNodeId = $this->getParentNodeId()
            ? '--parent_' . $this->getParentNodeId()
            : '';

        return [
            \Snowdog\Menu\Model\Menu::CACHE_TAG,
            'menu_' . $this->getMenu()->getId() . $parentNodeId,
            'store_' . $this->_storeManager->getStore()->getId(),
            'template_' . $this->getTemplate()
        ];
    }

    public function getMenuHtml($level = 0, $parent = null)
    {
        $nodes = $this->getNodes($level, $parent);
        $html = '';
        $i = 0;
        foreach ($nodes as $node) {
            $children = $this->getMenuHtml($level + 1, $node);
            $classes = [
                'level' . $level,
                $node->getClasses() ?: '',
            ];
            if (!empty($children)) {
                $classes[] = 'parent';
            }
            if ($i == 0) {
                $classes[] = 'first';
            }
            if ($i == count($nodes) - 1) {
                $classes[] = 'last';
            }
            if ($level == 0) {
                $classes[] = 'level-top';
            }
            $html .= '<li class="' . implode(' ', $classes) . '">';
            $html .= $this->renderNode($node, $level);
            if (!empty($children)) {
                $html .= '<ul class="level' . $level . ' submenu">';
                $html .= $children;
                $html .= '</ul>';
            }
            $html .= '</li>';
            ++$i;
        }
        return $html;
    }

    /**
     * @param NodeRepositoryInterface $node
     * @return string
     */
    public function renderMenuNode($node)
    {
        $nodeBlock = $this->getNodeTypeProvider($node->getType());

        $level = $node->getLevel();
        $isRoot = 0 == $level;

        $nodeBlock->setId($node->getNodeId())
            ->setTitle($node->getTitle())
            ->setLevel($level)
            ->setIsRoot($isRoot)
            ->setIsParent((bool) $node->getIsParent())
            ->setContent($node->getContent())
            ->setMenuClass($this->getMenu()->getCssClass());

        return $nodeBlock->toHtml();
    }

    /**
     * @param array $nodes
     * @param int $parentNodeId
     * @param int $level
     * @return string
     */
    public function renderSubmenu($nodes, $parentNodeId = 0, $level = 0)
    {
        $html = '';

        if ($nodes) {
            $block = clone $this;

            $block->setSubmenuNodes($nodes)
                ->setParentNodeId($parentNodeId)
                ->setLevel($level);

            $block->setTemplateContext($block);

            $html = $block->setTemplate($this->submenuTemplate)->toHtml();
        }

        return $html;
    }

    /**
     * @param int $level
     * @param NodeRepositoryInterface|null $parent
     * @return array
     */
    public function getNodesTree($level = 0, $parent = null)
    {
        $nodesTree = [];
        $nodes = $this->getNodes($level, $parent);

        foreach ($nodes as $node) {
            $nodesTree[] = [
                'node' => $node,
                'children' => $this->getNodesTree($level + 1, $node)
            ];
        }

        return $nodesTree;
    }

    /**
     * @param string $nodeType
     * @return \Snowdog\Menu\Api\NodeTypeInterface
     */
    public function getNodeTypeProvider($nodeType)
    {
        return $this->nodeTypeProvider->getProvider($nodeType);
    }

    public function getNodes($level = 0, $parent = null)
    {
        if (empty($this->nodes)) {
            $this->fetchData();
        }
        if (!isset($this->nodes[$level])) {
            return [];
        }
        $parentId = $parent['node_id'] ?: 0;
        if (!isset($this->nodes[$level][$parentId])) {
            return [];
        }
        return $this->nodes[$level][$parentId];
    }

    private function fetchData()
    {
        $nodes = $this->nodeRepository->getByMenu($this->getMenu()->getId());
        $result = [];
        $types = [];
        foreach ($nodes as $node) {
            $level = $node->getLevel();
            $parent = $node->getParentId() ?: 0;
            if (!isset($result[$level])) {
                $result[$level] = [];
            }
            if (!isset($result[$level][$parent])) {
                $result[$level][$parent] = [];
            }
            $result[$level][$parent][] = $node;
            $type = $node->getType();
            if (!isset($types[$type])) {
                $types[$type] = [];
            }
            $types[$type][] = $node;
        }
        $this->nodes = $result;

        foreach ($types as $type => $nodes) {
            $this->nodeTypeProvider->prepareData($type, $nodes);
        }
    }

    private function renderNode($node, $level)
    {
        $type = $node->getType();
        return $this->nodeTypeProvider->render($type, $node->getId(), $level);
    }

}
