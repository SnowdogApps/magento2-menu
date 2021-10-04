<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export\Node;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Node;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Node\DataProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

class Tree
{
    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    public function __construct(DataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
    }

    public function get(array $nodes): array
    {
        $tree = [];
        $childNodesClusters = [];
        $pendingNodesChildClusters = [];

        foreach ($nodes as $node) {
            $nodeId = $node->getId();
            $parentId = $node->getParentId();
            $nodeData = $this->dataProcessor->getData($node->getData());

            $this->removeNodeExcludedFields($nodeData);

            if (!$parentId) {
                $tree[$nodeId] = $nodeData;

                if (isset($pendingNodesChildClusters[$nodeId])) {
                    $tree[$nodeId][ExtendedFields::NODES] = $pendingNodesChildClusters[$nodeId];
                }

                continue;
            }

            $childNodesClusters[$nodeId] = $nodeData;

            if (isset($pendingNodesChildClusters[$nodeId])) {
                $childNodesClusters[$nodeId][ExtendedFields::NODES] = $pendingNodesChildClusters[$nodeId];
            }

            if (isset($tree[$parentId])) {
                $tree[$parentId][ExtendedFields::NODES][$nodeId] = &$childNodesClusters[$nodeId];
                continue;
            }

            if (isset($childNodesClusters[$parentId])) {
                $childNodesClusters[$parentId][ExtendedFields::NODES][$nodeId] = &$childNodesClusters[$nodeId];
                continue;
            }

            $pendingNodesChildClusters[$parentId][$nodeId] = &$childNodesClusters[$nodeId];
        }

        return $this->reindexTreeNodes($tree);
    }

    private function removeNodeExcludedFields(array &$data): void
    {
        foreach (Node::EXCLUDED_FIELDS as $excludedField) {
            unset($data[$excludedField]);
        }
    }

    private function reindexTreeNodes(array $nodes): array
    {
        $nodesCount = count($nodes);
        $tree = $nodesCount ? array_fill(0, $nodesCount, []) : [];

        foreach ($nodes as $node) {
            if (isset($node[ExtendedFields::NODES])) {
                $node[ExtendedFields::NODES] = $this->reindexTreeNodes($node[ExtendedFields::NODES]);
            }

            $position = $node[NodeInterface::POSITION];
            unset($node[NodeInterface::POSITION]);

            $tree[$position] = $node;
        }

        return $tree;
    }
}
