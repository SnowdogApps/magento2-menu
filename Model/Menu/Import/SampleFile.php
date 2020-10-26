<?php

namespace Snowdog\Menu\Model\Menu\Import;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\Menu\ExportProcessor;
use Snowdog\Menu\Model\NodeTypeProvider;
use Snowdog\Menu\Model\ResourceModel\Menu as MenuResource;
use Snowdog\Menu\Model\ResourceModel\Menu\Node as NodeResource;

class SampleFile
{
    const MENU_EXCLUDED_FIELDS = [
        MenuInterface::MENU_ID,
        MenuInterface::CREATION_TIME,
        MenuInterface::UPDATE_TIME
    ];

    const NODE_EXCLUDED_FIELDS = [
        NodeInterface::MENU_ID,
        NodeInterface::CREATION_TIME,
        NodeInterface::UPDATE_TIME
    ];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ExportProcessor
     */
    private $exportProcessor;

    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    /**
     * @var MenuResource
     */
    private $menuResource;

    /**
     * @var NodeResource
     */
    private $nodeResource;

    public function __construct(
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        ExportProcessor $exportProcessor,
        NodeTypeProvider $nodeTypeProvider,
        MenuResource $menuResource,
        NodeResource $nodeResource
    ) {
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->exportProcessor = $exportProcessor;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->menuResource = $menuResource;
        $this->nodeResource = $nodeResource;
    }

    /**
     * @return array
     */
    public function getFileDownloadContent()
    {
        $data = $this->getSampleData();
        return $this->exportProcessor->generateCsvDownloadFile('sample', $data, array_keys($data));
    }

    /**
     * @return array
     */
    private function getSampleData()
    {
        $data = $this->getMenuData();

        $data[ExportProcessor::STORES_CSV_FIELD] = $this->getStores();
        $data[ExportProcessor::NODES_CSV_FIELD] = $this->getNodeData();

        return $data;
    }

    /**
     * @return array
     */
    private function getMenuData()
    {
        return $this->getFieldsData($this->menuResource->getFields(), self::MENU_EXCLUDED_FIELDS);
    }

    /**
     * @return string
     */
    private function getNodeData()
    {
        $nodeTypes = array_keys($this->nodeTypeProvider->getLabels());
        $nodeData = [NodeInterface::TYPE => 'possible values: <' . implode('|', $nodeTypes) . '>'];

        $data = $this->getFieldsData(
            $this->nodeResource->getFields(),
            self::NODE_EXCLUDED_FIELDS,
            $nodeData
        );

        return $this->serializer->serialize([$data]);
    }

    /**
     * @return array
     */
    private function getFieldsData(array $fields, array $excludedFields = [], array $defaultData = [])
    {
        $fieldsData = [];
        $excludedFields = array_flip($excludedFields);

        foreach ($fields as $field => $description) {
            if (isset($excludedFields[$field])) {
                continue;
            }

            if (array_key_exists($field, $defaultData)) {
                $fieldsData[$field] = $defaultData[$field];
                continue;
            }

            if ($description['DEFAULT']) {
                $fieldsData[$field] = $description['DEFAULT'];
                continue;
            }

            if (strpos($description['DATA_TYPE'], 'int') !== false) {
                $fieldsData[$field] = '<integer>';
                continue;
            }

            if ($description['DATA_TYPE'] === 'varchar' || $description['DATA_TYPE'] === 'text') {
                $fieldsData[$field] = '<string>';
            }
        }

        return $fieldsData;
    }

    /**
     * @return string
     */
    private function getStores()
    {
        $stores = array_keys($this->storeManager->getStores(false));

        if (count($stores) === 1) {
            $stores[] = $stores[0] + 1;
        }

        return implode(',', $stores);
    }
}
