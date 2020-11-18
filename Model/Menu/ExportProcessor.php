<?php

namespace Snowdog\Menu\Model\Menu;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Symfony\Component\Yaml\Yaml;

class ExportProcessor
{
    const EXPORT_DIR = 'importexport';

    const YAML_INLINE_LEVEL = 10;
    const YAML_INDENTATION = 2;

    const STORES_FIELD = 'stores';
    const NODES_FIELD = 'nodes';

    const MENU_EXCLUDED_FIELDS = [
        MenuInterface::MENU_ID
    ];

    const MENU_RELATION_TABLES_FIELDS = [
        self::STORES_FIELD,
        self::NODES_FIELD
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

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filesystem $filesystem,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param int $menuId
     * @return array
     */
    public function getExportFileDownloadContent($menuId)
    {
        $data = $this->getExportData($menuId);
        return $this->generateDownloadFile($data[MenuInterface::IDENTIFIER], $data);
    }

    /**
     * @param string $fileId
     * @return array
     */
    public function generateDownloadFile($fileId, array $data)
    {
        $data = Yaml::dump($data, self::YAML_INLINE_LEVEL, self::YAML_INDENTATION);
        $file = $this->getDownloadFile($fileId);

        $this->directory->create(self::EXPORT_DIR);
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $stream->write($data);

        $stream->unlock();
        $stream->close();

        return ['type' => 'filename', 'value' => $file, 'rm' => true];
    }

    /**
     * @param int $menuId
     * @return array
     */
    private function getExportData($menuId)
    {
        $menu = $this->menuRepository->getById($menuId);
        $stores = $menu->getStores();
        $data = $menu->getData();
        $nodes = $this->getMenuNodeList($menuId);

        $data[self::STORES_FIELD] = $stores;
        $data[self::NODES_FIELD] = $nodes ?: null;

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
                $nodesData[$parentId][self::NODES_FIELD][$nodeId] = &$childNodesClusters[$nodeId];
                continue;
            }

            if (isset($childNodesClusters[$parentId])) {
                $childNodesClusters[$parentId][self::NODES_FIELD][$nodeId] = &$childNodesClusters[$nodeId];
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
            if (isset($node[self::NODES_FIELD])) {
                $node[self::NODES_FIELD] = $this->reindexNodesList($node[self::NODES_FIELD]);
            }

            $data[] = $node;
        }

        return $data;
    }

    /**
     * @param string $fileId
     * @return string
     */
    private function getDownloadFile($fileId)
    {
        return self::EXPORT_DIR . DIRECTORY_SEPARATOR . $fileId . '-' . hash('sha256', microtime()) . '.yaml';
    }
}
