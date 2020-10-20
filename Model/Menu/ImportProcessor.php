<?php

namespace Snowdog\Menu\Model\Menu;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Serialize\SerializerInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\MenuInterfaceFactory;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\ImportFactory;
use Snowdog\Menu\Model\ResourceModel\Menu\Node as NodeResource;

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
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var ImportFactory
     */
    private $importFactory;

    /**
     * @var NodeResource
     */
    private $nodeResource;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SerializerInterface $serializer,
        MenuInterfaceFactory $menuFactory,
        MenuRepositoryInterface $menuRepository,
        ImportFactory $importFactory,
        NodeResource $nodeResource
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->serializer = $serializer;
        $this->menuFactory = $menuFactory;
        $this->menuRepository = $menuRepository;
        $this->importFactory = $importFactory;
        $this->nodeResource = $nodeResource;
    }

    /**
     * @return string
     */
    public function importCsv()
    {
        $menuData = $this->uploadFileAndGetData();
        $stores = explode(',', $menuData[ExportProcessor::STORES_CSV_FIELD]);
        $nodes = $this->getNodesJsonData($menuData[ExportProcessor::NODES_CSV_FIELD]);

        unset($menuData[ExportProcessor::STORES_CSV_FIELD], $menuData[ExportProcessor::NODES_CSV_FIELD]);

        $menu = $this->menuFactory->create();
        $menuData[MenuInterface::IDENTIFIER] = $this->getNewMenuIdentifier(
            $menuData[MenuInterface::IDENTIFIER]
        );

        $menu->setData($menuData)->save();
        $menu->saveStores($stores);

        foreach ($nodes as &$node) {
            $node[NodeInterface::MENU_ID] = $menu->getId();
        }

        $this->nodeResource->insertMultiple($nodes);

        return $menu->getIdentifier();
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
