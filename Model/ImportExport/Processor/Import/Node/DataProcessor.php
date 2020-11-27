<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Import\FieldProcessor\Boolean as BooleanField;

class DataProcessor
{
    /**
     * @var BooleanField
     */
    private $booleanField;

    /**
     * @var TypeContent
     */
    private $typeContent;

    public function __construct(BooleanField $booleanField, TypeContent $typeContent)
    {
        $this->booleanField = $booleanField;
        $this->typeContent = $typeContent;
    }

    /**
     * @param int $menuId
     * @param int $nodesLevel
     * @param int|null $parentId
     * @return array
     */
    public function get(array $data, $menuId, $nodesLevel = 0, $parentId = null)
    {
        $data[NodeInterface::MENU_ID] = $menuId;
        $data[NodeInterface::PARENT_ID] = $parentId;
        $data[NodeInterface::LEVEL] = $nodesLevel;

        if (isset($data[NodeInterface::CONTENT]) && $data[NodeInterface::CONTENT] !== '') {
            $data[NodeInterface::CONTENT] = $this->typeContent->get(
                $data[NodeInterface::TYPE],
                $data[NodeInterface::CONTENT]
            );
        }

        if (isset($data[NodeInterface::TARGET])) {
            $data[NodeInterface::TARGET] = $this->booleanField->getValue($data[NodeInterface::TARGET]);
        }

        if (isset($data[NodeInterface::IS_ACTIVE])) {
            $data[NodeInterface::IS_ACTIVE] = $this->booleanField->getValue($data[NodeInterface::IS_ACTIVE]);
        }

        unset($data[ExtendedFields::NODES]);

        return $data;
    }
}
