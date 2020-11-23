<?php

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\ExportFile;

class Export
{
    const MENU_EXCLUDED_FIELDS = [
        MenuInterface::MENU_ID
    ];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var ExportFile
     */
    private $exportFile;

    /**
     * @var Import\Menu\Store
     */
    private $store;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        ExportFile $exportFile,
        Import\Menu\Store $store
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->exportFile = $exportFile;
        $this->store = $store;
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
        $menu = $this->menuRepository->getById($menuId);
        $data = $menu->getData();
        $nodes = $this->getMenuNodeList($menuId);

        $data[ExtendedFields::STORES] = $this->getStoreCodes($menu->getStores());

        if ($nodes) {
            $data[ExtendedFields::NODES] = $nodes;
        }

        unset($data[MenuInterface::MENU_ID]);

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

    /**
     * @return array
     */
    private function getStoreCodes(array $stores)
    {
        $storeCodes = [];

        foreach ($stores as $storeId) {
            $storeCodes[] = $this->store->get($storeId)->getCode();
        }

        return $storeCodes;
    }
}
