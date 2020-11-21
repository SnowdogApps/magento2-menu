<?php

namespace Snowdog\Menu\Model\ImportExport\Import;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\ExportProcessor;
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
        NodeInterface::NODE_ID,
        NodeInterface::MENU_ID,
        NodeInterface::PARENT_ID,
        NodeInterface::LEVEL,
        NodeInterface::CREATION_TIME,
        NodeInterface::UPDATE_TIME
    ];

    const STORES_DATA = ['<A store code/ID>', '<Another store code/ID>'];

    const BOOLEAN_TYPES = ['smallint', 'tinyint'];
    const BOOLEAN_FIELD_DEFAULT_VALUE = 'Valid values: <1 | 0>';

    const NODE_DEFAULT_DATA = [
        NodeInterface::TYPE => 'Available types: <{types}>',
        NodeInterface::CONTENT => 'Examples: <Category ID | Product SKU | CMS page/block identifier/ID | URL>',
        NodeInterface::TARGET => 'URL HTML anchor target. ' . self::BOOLEAN_FIELD_DEFAULT_VALUE
    ];

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
        ExportProcessor $exportProcessor,
        NodeTypeProvider $nodeTypeProvider,
        MenuResource $menuResource,
        NodeResource $nodeResource
    ) {
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
        return $this->exportProcessor->generateDownloadFile('sample', $data);
    }

    /**
     * @return array
     */
    private function getSampleData()
    {
        $data = $this->getMenuData();

        $data[ExportProcessor::STORES_FIELD] = self::STORES_DATA;
        $data[ExportProcessor::NODES_FIELD] = $this->getNodesData();

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
    private function getNodesData()
    {
        $defaultData = self::NODE_DEFAULT_DATA;
        $defaultData[NodeInterface::TYPE] = $this->getNodeTypeDefaultValue();

        $node = $this->getFieldsData(
            $this->nodeResource->getFields(),
            self::NODE_EXCLUDED_FIELDS,
            $defaultData
        );

        $node2 = $node;
        $node[ExportProcessor::NODES_FIELD] = [$node, $node];
        $data = [$node, $node2];

        return $data;
    }

    /**
     * @return string
     */
    private function getNodeTypeDefaultValue()
    {
        $nodeTypes = array_keys($this->nodeTypeProvider->getLabels());

        return strtr(
            self::NODE_DEFAULT_DATA[NodeInterface::TYPE],
            ['{types}' => implode(' | ', $nodeTypes)]
        );
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

            if (in_array($description['DATA_TYPE'], self::BOOLEAN_TYPES)) {
                $fieldsData[$field] = self::BOOLEAN_FIELD_DEFAULT_VALUE;
                continue;
            }

            $fieldsData[$field] = '<' . $description['DATA_TYPE'] . '>';
        }

        return $fieldsData;
    }
}
