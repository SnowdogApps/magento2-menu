<?php

namespace Snowdog\Menu\Block;

use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaFactory;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
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
     * @var Registry
     */
    private $coreRegistry;

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
        Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->coreRegistry = $coreRegistry;
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
        $parentNodeId = $this->getParentNode()
            ? '--parent_' . $this->getParentNode()->getNodeId()
            : '';

        $info = [
            \Snowdog\Menu\Model\Menu::CACHE_TAG,
            'menu_' . $this->getMenu()->getId() . $parentNodeId,
            'store_' . $this->_storeManager->getStore()->getId(),
            'template_' . $this->getTemplate()
        ];

        $pageIdentifier = $this->getCachePageIdentifier();
        if ($pageIdentifier) {
            $info['page_'] = $pageIdentifier;
        }

        return $info;
    }

    /**
     * @return string
     */
    private function getCachePageIdentifier()
    {
        $identifier = '';

        switch ($this->getRequest()->getRouteName()) {
            case 'cms':
                $pageId = $this->getRequest()->getParam('page_id');
                if ($pageId) {
                    $identifier = 'cms-page-' . $pageId;
                }
                break;
            case 'catalog':
                $category = $this->coreRegistry->registry('current_category');
                if ($category) {
                    $identifier = 'category-' . $category->getId();
                }
                break;
        }

        return $identifier;
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
     * @param string $nodeType
     * @return bool
     */
    public function isViewAllLinkAllowed($nodeType)
    {
        return $this->getNodeTypeProvider($nodeType)->isViewAllLinkAllowed();
    }

    /**
     * @param NodeRepositoryInterface $node
     * @return string
     */
    public function renderViewAllLink($node)
    {
        $nodeBlock = $this->getMenuNodeBlock($node)
            ->setIsViewAllLink(true);

        return $nodeBlock->toHtml();
    }

    /**
     * @param NodeRepositoryInterface $node
     * @return string
     */
    public function renderMenuNode($node)
    {
        return $this->getMenuNodeBlock($node)->toHtml();
    }

    /**
     * @param array $nodes
     * @param NodeRepositoryInterface $parentNode
     * @param int $level
     * @return string
     */
    public function renderSubmenu($nodes, $parentNode, $level = 0)
    {
        return $nodes
            ? $this->getSubmenuBlock($nodes, $parentNode, $level)->toHtml()
            : '';
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

    /**
     * @param NodeRepositoryInterface $node
     * @return Template
     */
    private function getMenuNodeBlock($node)
    {
        $nodeBlock = $this->getNodeTypeProvider($node->getType());

        $level = $node->getLevel();
        $isRoot = 0 == $level;

        $nodeBlock->setId($node->getNodeId())
            ->setTitle($node->getTitle())
            ->setLevel($level)
            ->setIsRoot($isRoot)
            ->setIsParent((bool) $node->getIsParent())
            ->setIsViewAllLink(false)
            ->setContent($node->getContent())
            ->setMenuClass($this->getMenu()->getCssClass());

        return $nodeBlock;
    }

    /**
     * @param array $nodes
     * @param NodeRepositoryInterface $parentNode
     * @param int $level
     * @return Menu
     */
    private function getSubmenuBlock($nodes, $parentNode, $level = 0)
    {
        $block = clone $this;

        $block->setSubmenuNodes($nodes)
            ->setParentNode($parentNode)
            ->setLevel($level);

        $block->setTemplateContext($block);
        $block->setTemplate($block->submenuTemplate);

        return $block;
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
