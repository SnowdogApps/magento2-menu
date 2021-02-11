<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Menu as MenuProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node as NodeProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;

class Import
{
    /**
     * @var MenuProcessor
     */
    private $menuProcessor;

    /**
     * @var NodeProcessor
     */
    private $nodeProcessor;

    /**
     * @var ValidationAggregateError
     */
    private $validationAggregateError;

    public function __construct(
        MenuProcessor $menuProcessor,
        NodeProcessor $nodeProcessor,
        ValidationAggregateError $validationAggregateError
    ) {
        $this->menuProcessor = $menuProcessor;
        $this->nodeProcessor = $nodeProcessor;
        $this->validationAggregateError = $validationAggregateError;
    }

    public function importData(array $data): string
    {
        $this->validateData($data);
        $menu = $this->createMenu($data);

        if (isset($data[ExtendedFields::NODES])) {
            $this->nodeProcessor->createNodes($data[ExtendedFields::NODES], (int) $menu->getId());
        }

        return $menu->getIdentifier();
    }

    private function createMenu(array $data): MenuInterface
    {
        $stores = $data[ExtendedFields::STORES];

        foreach (ExtendedFields::FIELDS as $extendedField) {
            unset($data[$extendedField]);
        }

        return $this->menuProcessor->createMenu($data, $stores);
    }

    /**
     * @throws ValidationAggregateError
     */
    private function validateData(array $data): void
    {
        $this->menuProcessor->validateImportData($data);

        if (isset($data[ExtendedFields::NODES])) {
            $this->nodeProcessor->validateImportData($data[ExtendedFields::NODES]);
        }

        if ($this->validationAggregateError->getErrors()) {
            throw $this->validationAggregateError;
        }
    }
}
