<?php

declare(strict_types=1);

namespace Snowdog\Menu\Service\Menu;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Message\ManagerInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\Menu\Node\Image\File as NodeImageFile;
use Snowdog\Menu\Model\Menu\Node\Image\Node as ImageNode;
use Snowdog\Menu\Model\Menu\Node\Validator as NodeValidator;
use Snowdog\Menu\Model\Menu\NodeFactory;
use Snowdog\Menu\Service\Menu\Nodes as MenuNodes;

class SaveRequestProcessor
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var NodeImageFile
     */
    private $nodeImageFile;

    /**
     * @var ImageNode
     */
    private $imageNode;

    /**
     * @var NodeValidator
     */
    private $nodeValidator;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var MenuNodes
     */
    private $menuNodes;

    public function __construct(
        ManagerInterface $messageManager,
        NodeRepositoryInterface $nodeRepository,
        NodeImageFile $nodeImageFile,
        ImageNode $imageNode,
        NodeValidator $nodeValidator,
        NodeFactory $nodeFactory,
        MenuNodes $menuNodes
    ) {
        $this->messageManager = $messageManager;
        $this->nodeRepository = $nodeRepository;
        $this->nodeImageFile = $nodeImageFile;
        $this->imageNode = $imageNode;
        $this->nodeValidator = $nodeValidator;
        $this->nodeFactory = $nodeFactory;
        $this->menuNodes = $menuNodes;
    }

    public function saveData(MenuInterface $menu, array $nodes = []): void
    {
        $existingNodes = [];
        $nodesToDelete = [];

        foreach ($this->menuNodes->getList($menu) as $node) {
            $existingNodes[$node->getId()] = $node;
        }

        foreach ($existingNodes as $nodeId => $node) {
            $nodesToDelete[$nodeId] = true;
        }

        $nodes = $this->convertTree($nodes, '#');
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
                continue;
            }

            if (!isset($invalidNodes[$nodeId])) {
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
            $parents = array_keys($path);
            $parent = array_pop($parents);

            while ($parent != $node['parent']) {
                array_pop($path);
                $parent = array_pop($parents);
            }

            if (isset($invalidNodes[$node['id']])) {
                if (!isset($existingNodes[$node['id']])) {
                    $path[$node['id']] = 0;
                    continue;
                }

                // Reset the invalid node content and save the rest of the node new data.
                // An error message will be printed to ask the user to fix the invalid node content.
                $node['content'] = $nodeMap[$node['id']]->getContent();
            }

            $nodeObject = $nodeMap[$node['id']];

            $this->processNodeObject($nodeObject, $node, $menu, $path, $nodeMap);
            $this->nodeRepository->save($nodeObject);

            $path[$node['parent']]++;
            $path[$node['id']] = 0;
        }
    }

    private function processNodeObject(
        NodeInterface $nodeObject,
        array $nodeData,
        MenuInterface $menu,
        array $path,
        array $nodeMap
    ): void {
        $level = count($path) - 1;
        $position = $path[$nodeData['parent']];

        $nodeObject->setParentId($nodeData['parent'] != '#' ? $nodeMap[$nodeData['parent']]->getId() : null);
        $nodeObject->setType($nodeData['type']);

        if (isset($nodeData['classes'])) {
            $nodeObject->setClasses($nodeData['classes']);
        }

        if (isset($nodeData['content'])) {
            $nodeObject->setContent($nodeData['content']);
        }

        if (isset($nodeData['target'])) {
            $nodeObject->setTarget($nodeData['target']);
        }

        $nodeTemplate = null;
        if (isset($nodeData['node_template']) && $nodeData['type'] != $nodeData['node_template']) {
            $nodeTemplate = $nodeData['node_template'];
        }

        $submenuTemplate = null;
        if (isset($nodeData['submenu_template']) && $nodeData['submenu_template'] != 'sub_menu') {
            $submenuTemplate = $nodeData['submenu_template'];
        }

        $nodeObject->setNodeTemplate($nodeTemplate);
        $nodeObject->setSubmenuTemplate($submenuTemplate);
        $nodeObject->setMenuId($menu->getMenuId());
        $nodeObject->setTitle($nodeData['title']);
        $nodeObject->setIsActive($nodeData['is_active'] ?? '0');
        $nodeObject->setLevel((string) $level);
        $nodeObject->setPosition((string) $position);

        if ($nodeObject->getImage() && empty($nodeData['image'])) {
            $this->nodeImageFile->delete($nodeObject->getImage());
        }

        $nodeObject->setImage($nodeData['image'] ?? null);
        $nodeObject->setImageAltText($nodeData['image_alt_text'] ?? null);

        $nodeObject->setSelectedItemId($nodeData['selected_item_id'] ?? null);
    }

    /**
     * @param int|string $parent
     */
    private function convertTree(array $nodes, $parent): array
    {
        $convertedTree = [];

        foreach ($nodes as $node) {
            $node['parent'] = $parent;
            $convertedTree[] = $node;
            // TODO: Refactor this code, to not merge arrays inside forEach
            // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $convertedTree = array_merge($convertedTree, $this->convertTree($node['columns'], $node['id']));
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
