<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\ExportFile;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\NodeTypeProvider;
use Snowdog\Menu\Model\ResourceModel\Menu as MenuResource;
use Snowdog\Menu\Model\ResourceModel\Menu\Node as NodeResource;

class SampleFile
{
    const DOWNLOAD_FILE_ID = 'sample';

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
     * @var ExportFile
     */
    private $exportFile;

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
        ExportFile $exportFile,
        NodeTypeProvider $nodeTypeProvider,
        MenuResource $menuResource,
        NodeResource $nodeResource
    ) {
        $this->exportFile = $exportFile;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->menuResource = $menuResource;
        $this->nodeResource = $nodeResource;
    }

    public function getSampleData(): array
    {
        $data = $this->getMenuData();

        $data[ExtendedFields::STORES] = self::STORES_DATA;
        $data[ExtendedFields::NODES] = $this->getNodesData();

        return $data;
    }

    private function getMenuData(): array
    {
        return $this->getFieldsData($this->menuResource->getFields(), self::MENU_EXCLUDED_FIELDS);
    }

    private function getNodesData(): array
    {
        $defaultData = self::NODE_DEFAULT_DATA;
        $defaultData[NodeInterface::TYPE] = $this->getNodeTypeDefaultValue();

        $node = $this->getFieldsData(
            $this->nodeResource->getFields(),
            self::NODE_EXCLUDED_FIELDS,
            $defaultData
        );

        // Create a tree of nodes.
        $node2 = $node;
        $node[ExtendedFields::NODES] = [$node, $node];
        $data = [$node, $node2];

        return $data;
    }

    private function getNodeTypeDefaultValue(): string
    {
        $nodeTypes = array_keys($this->nodeTypeProvider->getLabels());

        return strtr(
            self::NODE_DEFAULT_DATA[NodeInterface::TYPE],
            ['{types}' => implode(' | ', $nodeTypes)]
        );
    }

    private function getFieldsData(array $fields, array $excludedFields = [], array $defaultData = []): array
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
