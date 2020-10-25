<?php

namespace Snowdog\Menu\Model\Menu;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Serialize\SerializerInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\MenuInterfaceFactory;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\Data\NodeInterfaceFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportFactory;

class ImportProcessor
{
    const REQUIRED_FIELDS = [
        MenuInterface::TITLE,
        MenuInterface::IDENTIFIER,
        MenuInterface::CSS_CLASS,
        MenuInterface::IS_ACTIVE,
        ExportProcessor::STORES_CSV_FIELD
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
     * @var MenuInterfaceFactory
     */
    private $menuFactory;

    /**
     * @var NodeInterfaceFactory
     */
    private $nodeFactory;

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var ImportFactory
     */
    private $importFactory;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SerializerInterface $serializer,
        MenuInterfaceFactory $menuFactory,
        NodeInterfaceFactory $nodeFactory,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        ImportFactory $importFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->serializer = $serializer;
        $this->menuFactory = $menuFactory;
        $this->nodeFactory = $nodeFactory;
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->importFactory = $importFactory;
    }

    /**
     * @return string
     */
    public function importCsv()
    {
        $data = $this->uploadFileAndGetData();
        $menu = $this->createMenu($data);

        $this->createNodes($data[ExportProcessor::NODES_CSV_FIELD], $menu->getId());

        return $menu->getIdentifier();
    }

    /**
     * @return MenuInterface
     */
    private function createMenu(array $data)
    {
        $stores = $data[ExportProcessor::STORES_CSV_FIELD];
        unset($data[ExportProcessor::STORES_CSV_FIELD], $data[ExportProcessor::NODES_CSV_FIELD]);

        $menu = $this->menuFactory->create();
        $menuData[MenuInterface::IDENTIFIER] = $this->getNewMenuIdentifier($data[MenuInterface::IDENTIFIER]);

        $menu->setData($menuData);
        $this->menuRepository->save($menu);
        $menu->saveStores($stores);

        return $menu;
    }

    /**
     * @param int $menuId
     */
    private function createNodes(array $nodes, $menuId)
    {
        $newNodesIds = [];

        foreach ($nodes as $nodeData) {
            $node = $this->nodeFactory->create();

            $newNodeData = $nodeData;
            $newNodeData[NodeInterface::MENU_ID] = $menuId;
            $newNodeData[NodeInterface::PARENT_ID] = $newNodesIds[$nodeData[NodeInterface::PARENT_ID]] ?? null;

            unset($newNodeData[NodeInterface::NODE_ID]);

            $node->setData($newNodeData);
            $this->nodeRepository->save($node);

            $newNodesIds[$nodeData[NodeInterface::NODE_ID]] = $node->getId();
        }
    }

    /**
     * @param string $identifier
     * @return string
     */
    private function getNewMenuIdentifier($identifier)
    {
        $menus = $this->getMenuListByIdentifier($identifier);
        $identifiers = [];

        foreach ($menus as $menu) {
            $identifiers[$menu->getIdentifier()] = $menu->getId();
        }

        while (isset($identifiers[$identifier])) {
            $identifier .= '-1';
        }

        return $identifier;
    }

    /**
     * @param string $identifier
     * @return array
     */
    private function getMenuListByIdentifier($identifier)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(MenuInterface::IDENTIFIER, "${identifier}%", 'like')
            ->create();

        return $this->menuRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param array $data
     * @throws ValidatorException
     */
    private function validateImportData($data)
    {
        $missingFields = [];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if ($missingFields) {
            throw new ValidatorException(
                __('The following required import fields are missing: %1.', implode(', ', $missingFields))
            );
        }
    }

    /**
     * @throws ValidatorException
     * @return array
     */
    private function uploadFileAndGetData()
    {
        $import = $this->importFactory->create();

        try {
            $source = $import->uploadFileAndGetSource();
        } catch (LocalizedException $exception) {
            throw new ValidatorException(__($exception->getMessage()));
        }

        $source->rewind();
        $data = $source->current();

        $this->validateImportData($data);
        $import->deleteImportFile();

        $data[ExportProcessor::STORES_CSV_FIELD] = explode(',', $data[ExportProcessor::STORES_CSV_FIELD]);
        $data[ExportProcessor::NODES_CSV_FIELD] = $this->getNodesJsonData($data[ExportProcessor::NODES_CSV_FIELD]);

        return $data;
    }

    /**
     * @param string $data
     * @throws ValidatorException
     * @return array
     */
    private function getNodesJsonData($data)
    {
        try {
            return $this->serializer->unserialize($data);
        } catch (\InvalidArgumentException $exception) {
            throw new ValidatorException(__('Invalid menu nodes JSON format.'));
        }
    }
}
