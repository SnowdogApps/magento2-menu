<?php

namespace Snowdog\Menu\Model\Menu;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\SerializerInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;

class ExportProcessor
{
    const EXPORT_DIR = 'importexport';
    const STORES_CSV_FIELD = 'stores';
    const NODES_CSV_FIELD = 'nodes';
    const CSV_HEADERS = [
        MenuInterface::TITLE,
        MenuInterface::IDENTIFIER,
        MenuInterface::CSS_CLASS,
        MenuInterface::CREATION_TIME,
        MenuInterface::UPDATE_TIME,
        MenuInterface::IS_ACTIVE,
        self::STORES_CSV_FIELD,
        self::NODES_CSV_FIELD
    ];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SerializerInterface
     */
    private $serializer;

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
        SerializerInterface $serializer,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->serializer = $serializer;
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param int $menuId
     * @return array
     */
    public function generateCsvDownloadFile($menuId)
    {
        $data = $this->getExportData($menuId);
        $file = self::EXPORT_DIR . DIRECTORY_SEPARATOR
            . $data[MenuInterface::IDENTIFIER] . '-' . md5(microtime()) . '.csv';

        $this->directory->create(self::EXPORT_DIR);

        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $stream->writeCsv(self::CSV_HEADERS);
        $stream->writeCsv($data);

        $stream->unlock();
        $stream->close();

        return ['type' => 'filename', 'value' => $file, 'rm' => true];
    }

    /**
     * @param int $menuId
     * @return array
     */
    public function getExportData($menuId)
    {
        $menu = $this->menuRepository->getById($menuId);
        $stores = $menu->getStores();

        $data = $menu->getData();
        $nodes = $this->getMenuNodeList($menuId);

        $data[self::STORES_CSV_FIELD] = implode(',', $stores);
        $data[self::NODES_CSV_FIELD] = $nodes ? $this->serializer->serialize($nodes) : null;

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

        foreach ($nodes as $key => $node) {
            $nodesData[$key] = $node->getData();
            unset($nodesData[$key][NodeInterface::NODE_ID], $nodesData[$key][NodeInterface::MENU_ID]);
        }

        return $nodesData;
    }
}
