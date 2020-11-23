<?php

namespace Snowdog\Menu\Model\ImportExport\ImportProcessor\Menu;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\ImportProcessor\FieldProcessor\Boolean as BooleanField;

class DataProcessor
{
    /**
     * @var BooleanField
     */
    private $booleanField;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var Identifier
     */
    private $identifier;

    public function __construct(BooleanField $booleanField, Store $store, Identifier $identifier)
    {
        $this->booleanField = $booleanField;
        $this->store = $store;
        $this->identifier = $identifier;
    }

    /**
     * @return array
     */
    public function getMenuData(array $data)
    {
        $data[MenuInterface::IDENTIFIER] = $this->identifier->getNewIdentifier($data[MenuInterface::IDENTIFIER]);

        if (isset($data[MenuInterface::IS_ACTIVE])) {
            $data[MenuInterface::IS_ACTIVE] = $this->booleanField->getValue($data[MenuInterface::IS_ACTIVE]);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getStoreIds(array $storeCodes)
    {
        $storeIds = [];

        foreach ($storeCodes as $storeCode) {
            $storeIds[] = $this->store->get($storeCode)->getId();
        }

        return $storeIds;
    }
}
