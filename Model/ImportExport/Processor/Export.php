<?php

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\ExportFile;

class Export
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var ExportFile
     */
    private $exportFile;

    /**
     * @var Export\Menu
     */
    private $menu;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        NodeRepositoryInterface $nodeRepository,
        ExportFile $exportFile,
        Export\Menu $menu
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->nodeRepository = $nodeRepository;
        $this->exportFile = $exportFile;
        $this->menu = $menu;
    }

    /**
     * @param int $menuId
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function getDownloadFile($menuId)
    {
        return $this->exportFile->generateDownloadFile($menuId, $this->getExportData($menuId));
    }

    /**
     * @param int $menuId
     * @return array
     */
    private function getExportData($menuId)
    {
        $data = $this->menu->getData($menuId);
        $nodes = $this->getMenuNodeList($menuId);

        if ($nodes) {
            $data[ExtendedFields::NODES] = $nodes;
        }

        return $data;
    }

    /**
     * @param int $menuId
     * @return array
     */
    private function getMenuNodeList($menuId)
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

            unset(
                $nodeData[NodeInterface::MENU_ID],
                $nodeData[NodeInterface::NODE_ID],
                $nodeData[NodeInterface::PARENT_ID],
                $nodeData[NodeInterface::LEVEL]
            );

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
