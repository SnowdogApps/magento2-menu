<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\NodeTypeProvider;
use Snowdog\Menu\Model\ResourceModel\Menu\Node as NodeResource;

class Node
{
    const EXCLUDED_FIELDS = [
        NodeInterface::NODE_ID,
        NodeInterface::MENU_ID,
        NodeInterface::PARENT_ID,
        NodeInterface::LEVEL,
        NodeInterface::POSITION,
        NodeInterface::CREATION_TIME,
        NodeInterface::UPDATE_TIME
    ];

    const DEFAULT_DATA = [
        NodeInterface::TYPE => 'Available types: <{types}>',
        NodeInterface::CONTENT => 'Examples: <Category ID | Product SKU | CMS page/block identifier/ID | URL>',
        NodeInterface::TARGET => 'URL HTML anchor target. ' . Processor::BOOLEAN_FIELD_DEFAULT_VALUE
    ];

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    /**
     * @var NodeResource
     */
    private $nodeResource;

    public function __construct(
        Processor $processor,
        NodeTypeProvider $nodeTypeProvider,
        NodeResource $nodeResource
    ) {
        $this->processor = $processor;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->nodeResource = $nodeResource;
    }

    public function getData(): array
    {
        $defaultData = self::DEFAULT_DATA;
        $defaultData[NodeInterface::TYPE] = $this->getNodeTypeDefaultValue();

        $node = $this->processor->getFieldsData(
            $this->nodeResource->getFields(),
            self::EXCLUDED_FIELDS,
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
            self::DEFAULT_DATA[NodeInterface::TYPE],
            ['{types}' => implode(' | ', $nodeTypes)]
        );
    }
}
