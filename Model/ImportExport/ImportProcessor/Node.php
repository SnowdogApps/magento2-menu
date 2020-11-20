<?php

namespace Snowdog\Menu\Model\ImportExport\ImportProcessor;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\Data\NodeInterfaceFactory;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\ExportProcessor;

class Node
{
    /**
     * @var NodeInterfaceFactory
     */
    private $nodeFactory;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var Node\Catalog
     */
    private $nodeCatalog;

    /**
     * @var Node\Cms
     */
    private $nodeCms;

    /**
     * @var Node\Validator
     */
    private $validator;

    public function __construct(
        NodeInterfaceFactory $nodeFactory,
        NodeRepositoryInterface $nodeRepository,
        Node\Catalog $nodeCatalog,
        Node\Cms $nodeCms,
        Node\Validator $validator
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->nodeRepository = $nodeRepository;
        $this->nodeCatalog = $nodeCatalog;
        $this->nodeCms = $nodeCms;
        $this->validator = $validator;
    }

    /**
     * @param int $menuId
     * @param int $nodesLevel
     * @param int|null $parentNode
     */
    public function createNodes(array $nodes, $menuId, $nodesLevel = 0, $parentNode = null)
    {
        foreach ($nodes as $nodeData) {
            $node = $this->nodeFactory->create();
            $data = $this->getProcessedNodeData($nodeData, $menuId, $nodesLevel, $parentNode);

            $node->setData($data);
            $this->nodeRepository->save($node);

            if (isset($nodeData[ExportProcessor::NODES_FIELD])) {
                $this->createNodes($nodeData[ExportProcessor::NODES_FIELD], $menuId, $nodesLevel + 1, $node->getId());
            }
        }
    }

    public function validateImportData(array $data)
    {
        $this->validator->validate($data);
    }

    /**
     * @param int $menuId
     * @param int $nodesLevel
     * @param int|null $parentId
     * @return array
     */
    private function getProcessedNodeData(array $data, $menuId, $nodesLevel = 0, $parentId = null)
    {
        $data[NodeInterface::MENU_ID] = $menuId;
        $data[NodeInterface::PARENT_ID] = $parentId;
        $data[NodeInterface::LEVEL] = $nodesLevel;

        $data[NodeInterface::CONTENT] = $this->getNodeTypeContent(
            $data[NodeInterface::TYPE],
            $data[NodeInterface::CONTENT]
        );

        if (isset($data[NodeInterface::TARGET])) {
            $data[NodeInterface::TARGET] = (bool) $data[NodeInterface::TARGET];
        }

        if (isset($data[NodeInterface::IS_ACTIVE])) {
            $data[NodeInterface::IS_ACTIVE] = (bool) $data[NodeInterface::IS_ACTIVE];
        }

        unset($data[ExportProcessor::NODES_FIELD]);

        return $data;
    }

    /**
     * @param string $type
     * @param string|int $content
     * @return string|int
     */
    private function getNodeTypeContent($type, $content)
    {
        switch ($type) {
            case Node\Catalog::PRODUCT_NODE_TYPE:
                $product = $this->nodeCatalog->getProduct($content);
                $content = $product->getId();

                break;
            case Node\Cms::BLOCK_NODE_TYPE:
                $block = $this->nodeCms->getBlock($content);
                $content = $block->getIdentifier();

                break;
            case Node\Cms::PAGE_NODE_TYPE:
                $page = $this->nodeCms->getPage($content);
                $content = $page->getIdentifier();

                break;
        }

        return $content;
    }
}
