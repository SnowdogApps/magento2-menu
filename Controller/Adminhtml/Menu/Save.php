<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Snowdog\Menu\Controller\Adminhtml\MenuAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\Search\FilterGroupBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\Menu\NodeFactory;
use Snowdog\Menu\Model\Menu\Node\Image\File as NodeImageFile;
use Snowdog\Menu\Model\Menu\Node\Image\Node as ImageNode;
use Snowdog\Menu\Model\MenuFactory;
use Snowdog\Menu\Model\Menu\Node\Validator as NodeValidator;
use Snowdog\Menu\Service\MenuHydrator;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

class Save extends MenuAction implements HttpPostActionInterface
{
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

    /** @var NodeValidator */
    private $nodeValidator;

    /** @var NodeImageFile */
    private $nodeImageFile;

    /** @var ImageNode */
    private $imageNode;

    /** @var MenuHydrator */
    private $hydrator;

    /**
     * @param Context $context
     * @param MenuRepositoryInterface $menuRepository
     * @param NodeRepositoryInterface $nodeRepository
     * @param FilterBuilderFactory $filterBuilderFactory
     * @param FilterGroupBuilderFactory $filterGroupBuilderFactory
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param NodeFactory $nodeFactory
     * @param MenuFactory $menuFactory
     * @param NodeValidator $nodeValidator
     * @param NodeImageFile $nodeImageFile
     * @param ImageNode $imageNode
     * @param MenuHydrator|null $hydrator
     */
    public function __construct(
        Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        FilterBuilderFactory $filterBuilderFactory,
        FilterGroupBuilderFactory $filterGroupBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        NodeFactory $nodeFactory,
        MenuFactory $menuFactory,
        NodeValidator $nodeValidator,
        NodeImageFile $nodeImageFile,
        ImageNode $imageNode,
        MenuHydrator $hydrator = null
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->filterGroupBuilderFactory = $filterGroupBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->nodeFactory = $nodeFactory;
        $this->nodeValidator = $nodeValidator;
        $this->nodeImageFile = $nodeImageFile;
        $this->imageNode = $imageNode;
        // Backwards compatible class loader
        $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(MenuHydrator::class);
        parent::__construct($context, $menuRepository, $menuFactory);
    }

    /**
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $menu = $this->getCurrentMenu();

        $this->hydrator->mapRequest($menu, $this->getRequest());
        $menu = $this->menuRepository->save($menu);

        $menu->saveStores($this->getRequest()->getParam('stores'));
        $nodes = $this->getRequest()->getParam('serialized_nodes');

        $existingNodes = [];
        foreach ($this->getCurrentNodes($menu) as $node) {
            $existingNodes[$node->getId()] = $node;
        }

        $nodesToDelete = [];
        foreach ($existingNodes as $nodeId => $node) {
            $nodesToDelete[$nodeId] = true;
        }

        $nodes = $nodes ? json_decode($nodes, true) : [];
        $nodes = $this->_convertTree($nodes, '#');
        $nodeMap = [];
        $invalidNodes = [];

        foreach ($nodes as $node) {
            $nodeId = $node['id'];

            if (!$this->validateNode($node)) {
                $invalidNodes[$nodeId] = $node;
            }

            if (isset($existingNodes[$nodeId])) {
                unset($nodesToDelete[$nodeId]);
                $nodeMap[$nodeId] = $existingNodes[$nodeId];
            } else {
                if (isset($invalidNodes[$nodeId])) {
                    continue;
                }

                $nodeObject = $this->nodeFactory->create();
                $nodeObject->setMenuId($menu->getMenuId());
                $nodeObject = $this->nodeRepository->save($nodeObject);
                $nodeMap[$nodeId] = $nodeObject;
            }
        }

        $nodesToDeleteIds = array_keys($nodesToDelete);
        $nodesToDeleteImages = $this->imageNode->getNodeListImages($nodesToDeleteIds);

        foreach ($nodesToDeleteIds as $nodeId) {
            $this->nodeRepository->deleteById($nodeId);

            if (isset($nodesToDeleteImages[$nodeId])) {
                $this->nodeImageFile->delete($nodesToDeleteImages[$nodeId]);
            }
        }

        $path = ['#' => 0];

        foreach ($nodes as $node) {
            if (isset($invalidNodes[$node['id']])) {
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

            $nodeTemplate = null;
            if (isset($node['node_template']) && $node['type'] != $node['node_template']) {
                $nodeTemplate = $node['node_template'];
            }
            $nodeObject->setNodeTemplate($nodeTemplate);

            $submenuTemplate = null;
            if (isset($node['submenu_template']) && $node['submenu_template'] != 'sub_menu') {
                $submenuTemplate = $node['submenu_template'];
            }
            $nodeObject->setSubmenuTemplate($submenuTemplate);

            $nodeObject->setMenuId($menu->getMenuId());
            $nodeObject->setTitle($node['title']);
            $nodeObject->setIsActive($node['is_active'] ?? '0');
            $nodeObject->setLevel((string) $level);
            $nodeObject->setPosition((string) $position);

            if ($nodeObject->getImage() && empty($node['image'])) {
                $this->nodeImageFile->delete($nodeObject->getImage());
            }

            $nodeObject->setImage($node['image'] ?? null);
            $nodeObject->setImageAltText($node['image_alt_text'] ?? null);

            $this->nodeRepository->save($nodeObject);

            $path[$node['id']] = 0;
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/index');

        if ($this->getRequest()->getParam('back')) {
            $redirect->setPath('*/*/edit', [
                self::ID => $menu->getMenuId(),
                '_current' => true
            ]);
        }

        return $redirect;
    }

    /**
     * @return array
     */
    private function getCurrentNodes(MenuInterface $menu)
    {
        $filterBuilder = $this->filterBuilderFactory->create();
        $filter = $filterBuilder->setField('menu_id')
            ->setValue($menu->getMenuId())
            ->setConditionType('eq')
            ->create();

        $filterGroupBuilder = $this->filterGroupBuilderFactory->create();
        $filterGroup = $filterGroupBuilder->addFilter($filter)->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->setFilterGroups([$filterGroup])->create();

        return $this->nodeRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param array $nodes
     * @param $parent
     * @return array
     */
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

    private function validateNode(array $node): bool
    {
        try {
            $this->nodeValidator->validate($node);
            $result = true;
        } catch (ValidatorException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $result = false;
        }

        return $result;
    }
}
