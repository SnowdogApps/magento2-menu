<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData\Node;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor\Field\Value as FieldValueProcessor;
use Snowdog\Menu\Model\NodeTypeProvider;

class DefaultData
{
    const CONTENT_DATA = [
        'Category ID',
        'Product SKU',
        'CMS page/block identifier/ID',
        'URL'
    ];

    const DEFAULT_DATA = [
        NodeInterface::TYPE => 'Available types: <{types}>',
        NodeInterface::CONTENT => 'Examples: <{content}>',
        NodeInterface::TARGET => 'URL HTML anchor target. ' . FieldValueProcessor::BOOLEAN_FIELD_DEFAULT_VALUE
    ];

    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    public function __construct(NodeTypeProvider $nodeTypeProvider)
    {
        $this->nodeTypeProvider = $nodeTypeProvider;
    }

    public function get(): array
    {
        $defaultData = self::DEFAULT_DATA;

        $defaultData[NodeInterface::TYPE] = $this->getNodeTypeDefaultValue();
        $defaultData[NodeInterface::CONTENT] = $this->getNodeContentDefaultValue();

        return $defaultData;
    }

    private function getNodeTypeDefaultValue(): string
    {
        $nodeTypes = array_keys($this->nodeTypeProvider->getLabels());
        return $this->getMultipleValuesFieldData(NodeInterface::TYPE, $nodeTypes, '{types}');
    }

    private function getNodeContentDefaultValue(): string
    {
        return $this->getMultipleValuesFieldData(NodeInterface::CONTENT, self::CONTENT_DATA, '{content}');
    }

    private function getMultipleValuesFieldData(string $field, array $values, string $placeholder): string
    {
        return strtr(self::DEFAULT_DATA[$field], [$placeholder => implode(' | ', $values)]);
    }
}
