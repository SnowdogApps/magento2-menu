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
    const CACHE_TAG = 'snowmenu';

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
    /**
     * @var SearchCriteriaFactory
     */
    private $searchCriteriaFactory;
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

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
        return [self::CACHE_TAG, Block::CACHE_TAG];
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
            if($level == 0) {
                $classes[] = 'level-top';
            }
            $html .= '<li class="' . implode(' ', $classes) . '">';
            $html .= $this->renderNode($node, $level);
            if (!empty($children)) {
                $html .= '<ul class="level'.$level.' submenu">';
                $html .= $children;
                $html .= '</ul>';
            }
            $html .= '</li>';
            ++$i;
        }
        return $html;
    }

    private function getNodes($level, $parent)
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
        $storeId = $this->_storeManager->getStore()->getId();
        $menu = $this->menuRepository->get($this->getData('menu'), $storeId);
        $nodes = $this->nodeRepository->getByMenu($menu->getId());
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