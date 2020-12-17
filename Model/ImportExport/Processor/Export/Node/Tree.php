<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export\Node;

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

        foreach ($nodes as $node) {
            $nodeId = $node->getId();
            $parentId = $node->getParentId();
            $nodeData = $this->dataProcessor->getData($node->getData());

            $this->removeNodeExcludedFields($nodeData);

            if (!$parentId) {
                $tree[$nodeId] = $nodeData;
                continue;
            }

            $childNodesClusters[$nodeId] = $nodeData;

            if (isset($tree[$parentId])) {
                $tree[$parentId][ExtendedFields::NODES][$nodeId] = &$childNodesClusters[$nodeId];
                continue;
            }

            if (isset($childNodesClusters[$parentId])) {
                $childNodesClusters[$parentId][ExtendedFields::NODES][$nodeId] = &$childNodesClusters[$nodeId];
                continue;
            }
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
        $tree = [];

        foreach ($nodes as $node) {
            if (isset($node[ExtendedFields::NODES])) {
                $node[ExtendedFields::NODES] = $this->reindexTreeNodes($node[ExtendedFields::NODES]);
            }

            $tree[] = $node;
        }

        return $tree;
    }
}
