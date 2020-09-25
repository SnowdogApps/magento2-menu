<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\Search\FilterGroupBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\Menu;
use Snowdog\Menu\Model\Menu\NodeFactory;
use Snowdog\Menu\Model\MenuFactory;
use Snowdog\Menu\Service\MenuHydrator;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /** @var MenuRepositoryInterface */
    private $menuRepository;

    /**  @var NodeRepositoryInterface */
    private $nodeRepository;

    /** @var FilterBuilderFactory */
    private $filterBuilderFactory;

    /** @var FilterGroupBuilderFactory */
    private $filterGroupBuilderFactory;

    /** @var SearchCriteriaBuilderFactory */
    private $searchCriteriaBuilderFactory;

    /** @var NodeFactory */
    private $nodeFactory;

    /** @var MenuFactory */
    private $menuFactory;

    /** @var ProductRepository */
    private $productRepository;

    /** @var MenuHydrator */
    private $hydrator;

    /**
     * @param Action\Context $context
     * @param MenuRepositoryInterface $menuRepository
     * @param NodeRepositoryInterface $nodeRepository
     * @param FilterBuilderFactory $filterBuilderFactory
     * @param FilterGroupBuilderFactory $filterGroupBuilderFactory
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param NodeFactory $nodeFactory
     * @param MenuFactory $menuFactory
     * @param ProductRepository $productRepository
     * @param MenuHydrator|null $hydrator
     */
    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        FilterBuilderFactory $filterBuilderFactory,
        FilterGroupBuilderFactory $filterGroupBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        NodeFactory $nodeFactory,
        MenuFactory $menuFactory,
        ProductRepository $productRepository,
        MenuHydrator $hydrator = null
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->filterGroupBuilderFactory = $filterGroupBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->nodeFactory = $nodeFactory;
        $this->menuFactory = $menuFactory;
        $this->productRepository = $productRepository;
        // Backwards compatible class loader
        $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(MenuHydrator::class);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $menu = $this->getCurrentMenu();

        $menu->setIsActive(1);
        $this->hydrator->mapRequest($menu, $this->getRequest());
        $menu = $this->menuRepository->save($menu);

        $menu->saveStores($this->getRequest()->getParam('stores'));
        $nodes = $this->getRequest()->getParam('serialized_nodes');

        $filterBuilder = $this->filterBuilderFactory->create();
        $filter = $filterBuilder->setField('menu_id')
            ->setValue($menu->getMenuId())
            ->setConditionType('eq')
            ->create();

        $filterGroupBuilder = $this->filterGroupBuilderFactory->create();
        $filterGroup = $filterGroupBuilder->addFilter($filter)->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->setFilterGroups([$filterGroup])->create();

        $oldNodes = $this->nodeRepository->getList($searchCriteria)->getItems();

        $existingNodes = [];

        foreach ($oldNodes as $node) {
            $existingNodes[$node->getId()] = $node;
        }

        $nodesToDelete = [];

        foreach ($existingNodes as $nodeId => $noe) {
            $nodesToDelete[$nodeId] = true;
        }

        $nodes = $nodes ? json_decode($nodes, true) : [];
        $nodes = $this->_convertTree($nodes, '#');
        $nodeMap = [];

        foreach ($nodes as $node) {
            $nodeId = $node['id'];

            if (in_array($nodeId, array_keys($existingNodes))) {
                unset($nodesToDelete[$nodeId]);
                $nodeMap[$nodeId] = $existingNodes[$nodeId];
            } else {
                $nodeObject = $this->nodeFactory->create();
                $nodeObject->setMenuId($menu->getMenuId());
                $nodeObject = $this->nodeRepository->save($nodeObject);
                $nodeMap[$nodeId] = $nodeObject;
            }
        }

        foreach (array_keys($nodesToDelete) as $nodeId) {
            $this->nodeRepository->deleteById($nodeId);
        }

        $path = ['#' => 0];

        foreach ($nodes as $node) {
            if ($node['type'] == 'product' && !$this->validateProductNode($node)) {
                continue;
            }

            $nodeObject = $nodeMap[$node['id']];
            $parents = array_keys($path);
            $parent = array_pop($parents);

            while ($parent != $node['parent']) {
                array_pop($path);
                $parent = array_pop($parents);
            }

            $level = count($path) - 1;
            $position = $path[$node['parent']]++;

            if ($node['parent'] == '#') {
                $nodeObject->setParentId(null);
            } else {
                $nodeObject->setParentId($nodeMap[$node['parent']]->getId());
            }

            $nodeObject->setType($node['type']);

            if (isset($node['classes'])) {
                $nodeObject->setClasses($node['classes']);
            }

            if (isset($node['content'])) {
                $nodeObject->setContent($node['content']);
            }

            if (isset($node['target'])) {
                $nodeObject->setTarget($node['target']);
            }

            $nodeObject->setMenuId($menu->getMenuId());
            $nodeObject->setTitle($node['title']);
            $nodeObject->setIsActive((int) ($node['is_active'] ?? 0));
            $nodeObject->setLevel($level);
            $nodeObject->setPosition($position);

            $this->nodeRepository->save($nodeObject);

            $path[$node['id']] = 0;
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/index');

        if ($this->getRequest()->getParam('back')) {
            $redirect->setPath('*/*/edit', ['id' => $menu->getMenuId(), '_current' => true]);
        }

        return $redirect;
    }

    protected function _convertTree($nodes, $parent)
    {
        $convertedTree = [];

        foreach ($nodes as $node) {
            $node['parent'] = $parent;
            $convertedTree[] = $node;
            // TODO: Refactor this code, to not merge arrays inside forEach
            // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $convertedTree = array_merge($convertedTree, $this->_convertTree($node['columns'], $node['id']));
        }

        return $convertedTree;
    }

    /**
     * @param array $node
     * @return bool
     */
    private function validateProductNode(array $node)
    {
        try {
            $this->productRepository->getById($node['content']);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Product does not exist'));
            return false;
        }

        return true;
    }

    /**
     * Returns menu model based on the Request (requested with `id` or fresh instance)
     *
     * @return Menu
     */
    private function getCurrentMenu(): Menu
    {
        $menuId = $this->getRequest()->getParam('id');

        if ($menuId) {
            return $this->menuRepository->getById($menuId);
        }

        return $this->menuFactory->create();
    }
}
