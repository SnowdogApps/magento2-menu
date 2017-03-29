<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\Search\FilterGroupBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\ResponseInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\Menu\NodeFactory;
use Snowdog\Menu\Model\MenuFactory;

class Save extends Action
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
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;
    /**
     * @var FilterGroupBuilderFactory
     */
    private $filterGroupBuilderFactory;
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;
    /**
     * @var NodeFactory
     */
    private $nodeFactory;
    /**
     * @var MenuFactory
     */
    private $menuFactory;

    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        FilterBuilderFactory $filterBuilderFactory,
        FilterGroupBuilderFactory $filterGroupBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        NodeFactory $nodeFactory,
        MenuFactory $menuFactory
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->filterGroupBuilderFactory = $filterGroupBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->nodeFactory = $nodeFactory;
        $this->menuFactory = $menuFactory;
    }


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $menu = $this->menuRepository->getById($id);
        } else {
            $menu = $this->menuFactory->create();
        }
        $menu->setTitle($this->getRequest()->getParam('title'));
        $menu->setIdentifier($this->getRequest()->getParam('identifier'));
        $menu->setCssClass($this->getRequest()->getParam('css_class'));
        $menu->setIsActive(1);
        $menu = $this->menuRepository->save($menu);

        if (!$id) {
            $id = $menu->getId();
        }

        $menu->saveStores($this->getRequest()->getParam('stores'));

        $nodes = $this->getRequest()->getParam('serialized_nodes');
        if (!empty($nodes)) {
            $nodes = json_decode($nodes, true);
            if (!empty($nodes)) {

                $filterBuilder = $this->filterBuilderFactory->create();
                $filter = $filterBuilder->setField('menu_id')->setValue($id)->setConditionType('eq')->create();

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

                $nodeMap = [];

                foreach ($nodes as $node) {
                    $nodeId = $node['id'];
                    $matches = [];
                    if (preg_match('/^node_([0-9]+)$/', $nodeId, $matches)) {
                        $nodeId = $matches[1];
                        unset($nodesToDelete[$nodeId]);
                        $nodeMap[$node['id']] = $existingNodes[$nodeId];
                    } else {
                        $nodeObject = $this->nodeFactory->create();
                        $nodeObject->setMenuId($id);
                        $nodeObject = $this->nodeRepository->save($nodeObject);
                        $nodeMap[$nodeId] = $nodeObject;
                    }
                }

                foreach (array_keys($nodesToDelete) as $nodeId) {
                    $this->nodeRepository->deleteById($nodeId);
                }


                $path = [
                    '#' => 0,
                ];
                foreach ($nodes as $node) {
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

                    $nodeObject->setType($node['data']['type']);
                    if (isset($node['data']['classes'])) {
                        $nodeObject->setClasses($node['data']['classes']);
                    }
                    if (isset($node['data']['content'])) {
                        $nodeObject->setContent($node['data']['content']);
                    }
                    $nodeObject->setMenuId($id);
                    $nodeObject->setTitle($node['text']);
                    $nodeObject->setIsActive(1);
                    $nodeObject->setLevel($level);
                    $nodeObject->setPosition($position);

                    $this->nodeRepository->save($nodeObject);

                    $path[$node['id']] = 0;
                }
            }
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/index');
        return $redirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Snowdog_Menu::menus');
    }
}
