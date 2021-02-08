<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Import\SampleData\Node\DefaultData as NodeDefaultData;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
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

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var NodeDefaultData
     */
    private $nodeDefaultData;

    /**
     * @var NodeResource
     */
    private $nodeResource;

    public function __construct(
        Processor $processor,
        NodeDefaultData $nodeDefaultData,
        NodeResource $nodeResource
    ) {
        $this->processor = $processor;
        $this->nodeDefaultData = $nodeDefaultData;
        $this->nodeResource = $nodeResource;
    }

    public function getData(): array
    {
        $node = $this->processor->getFieldsData(
            $this->nodeResource->getFields(),
            self::EXCLUDED_FIELDS,
            $this->nodeDefaultData->get()
        );

        return $this->getNodesTree($node);
    }

    private function getNodesTree(array $node): array
    {
        $node2 = $node;
        $node[ExtendedFields::NODES] = [$node, $node];
        
        return [$node, $node2];
    }
}
