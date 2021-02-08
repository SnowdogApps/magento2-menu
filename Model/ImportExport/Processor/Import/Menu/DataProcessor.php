<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Menu;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Import\FieldProcessor\Boolean as BooleanField;
use Snowdog\Menu\Model\ImportExport\Processor\Store;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Menu\Identifier;

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

    public function getMenuData(array $data): array
    {
        $data[MenuInterface::IDENTIFIER] = $this->identifier->getNewIdentifier(
            (string) $data[MenuInterface::IDENTIFIER]
        );

        if (isset($data[MenuInterface::IS_ACTIVE])) {
            $data[MenuInterface::IS_ACTIVE] = $this->booleanField->getValue($data[MenuInterface::IS_ACTIVE]);
        }

        return $data;
    }

    public function getStoreIds(array $storeCodes): array
    {
        $storeCodes = array_unique($storeCodes);
        $storeIds = [];

        foreach ($storeCodes as $storeCode) {
            $storeIds[] = (int) $this->store->get($storeCode)->getId();
        }

        return $storeIds;
    }
}
