<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;

class ImportProcessor
{
    /**
     * @var ImportSource
     */
    private $importSource;

    /**
     * @var Processor\Import\Menu
     */
    private $menuProcessor;

    /**
     * @var Processor\Import\Node
     */
    private $nodeProcessor;

    /**
     * @var ValidationAggregateError
     */
    private $validationAggregateError;

    public function __construct(
        ImportSource $importSource,
        Processor\Import\Menu $menuProcessor,
        Processor\Import\Node $nodeProcessor,
        ValidationAggregateError $validationAggregateError
    ) {
        $this->importSource = $importSource;
        $this->menuProcessor = $menuProcessor;
        $this->nodeProcessor = $nodeProcessor;
        $this->validationAggregateError = $validationAggregateError;
    }

    public function importFile(): string
    {
        $data = $this->uploadFileAndGetData();
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
     * @throws ValidatorException
     */
    private function uploadFileAndGetData(): array
    {
        try {
            $data = $this->importSource->uploadFileAndGetData();
        } catch (LocalizedException $exception) {
            throw new ValidatorException(__($exception->getMessage()));
        }

        $this->validateData($data);

        return $data;
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
