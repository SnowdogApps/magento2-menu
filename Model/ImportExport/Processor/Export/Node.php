<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Node\DataProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

class Node
{
    const EXCLUDED_FIELDS = [
        NodeInterface::MENU_ID,
        NodeInterface::NODE_ID,
        NodeInterface::PARENT_ID,
        NodeInterface::LEVEL,
        NodeInterface::POSITION
    ];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        NodeRepositoryInterface $nodeRepository,
        DataProcessor $dataProcessor
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->nodeRepository = $nodeRepository;
        $this->dataProcessor = $dataProcessor;
    }

    public function getList(int $menuId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(NodeInterface::MENU_ID, $menuId)
            ->create();

        $nodes = $this->nodeRepository->getList($searchCriteria)->getItems();

        $nodesData = [];
        $childNodesClusters = [];

        foreach ($nodes as $node) {
            $nodeId = $node->getId();
            $parentId = $node->getParentId();
            $nodeData = $this->dataProcessor->get($node->getData());

            $this->removeNodeExcludedFields($nodeData);

            if (!$parentId) {
                $nodesData[$nodeId] = $nodeData;
                continue;
            }

            $childNodesClusters[$nodeId] = $nodeData;

            if (isset($nodesData[$parentId])) {
                $nodesData[$parentId][ExtendedFields::NODES][$nodeId] = &$childNodesClusters[$nodeId];
                continue;
            }

            if (isset($childNodesClusters[$parentId])) {
                $childNodesClusters[$parentId][ExtendedFields::NODES][$nodeId] = &$childNodesClusters[$nodeId];
                continue;
            }
        }

        return $this->reindexNodesList($nodesData);
    }

    private function removeNodeExcludedFields(array &$data): void
    {
        foreach (self::EXCLUDED_FIELDS as $excludedField) {
            unset($data[$excludedField]);
        }
    }

    private function reindexNodesList(array $nodes): array
    {
        $data = [];

        foreach ($nodes as $node) {
            if (isset($node[ExtendedFields::NODES])) {
                $node[ExtendedFields::NODES] = $this->reindexNodesList($node[ExtendedFields::NODES]);
            }

            $data[] = $node;
        }

        return $data;
    }
}
