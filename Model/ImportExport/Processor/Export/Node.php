<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Export;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

class Node
{
    const EXCLUDED_FIELDS = [
        NodeInterface::MENU_ID,
        NodeInterface::NODE_ID,
        NodeInterface::PARENT_ID,
        NodeInterface::LEVEL
    ];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        NodeRepositoryInterface $nodeRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param int $menuId
     * @return array
     */
    public function getList($menuId)
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
            $nodeData = $node->getData();

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

    private function removeNodeExcludedFields(array &$data)
    {
        foreach (self::EXCLUDED_FIELDS as $excludedField) {
            unset($data[$excludedField]);
        }
    }

    /**
     * @return array
     */
    private function reindexNodesList(array $nodes)
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
