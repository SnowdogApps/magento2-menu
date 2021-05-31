<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Menu;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;
use Snowdog\Menu\Model\ImportExport\Processor\Store;

class Validator
{
    const REQUIRED_FIELDS = [
        MenuInterface::TITLE,
        MenuInterface::IDENTIFIER,
        MenuInterface::IS_ACTIVE,
        ExtendedFields::STORES
    ];

    /**
     * @var ValidationAggregateError
     */
    private $validationAggregateError;

    /**
     * @var Store
     */
    private $store;

    public function __construct(ValidationAggregateError $validationAggregateError, Store $store)
    {
        $this->validationAggregateError = $validationAggregateError;
        $this->store = $store;
    }

    public function validate(array $data): void
    {
        $this->validateRequiredFields($data);

        if (isset($data[ExtendedFields::STORES])) {
            $this->validateStores($data[ExtendedFields::STORES]);
        }
    }

    private function validateRequiredFields(array $data): void
    {
        $missingFields = [];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $missingFields[] = $field;
            }
        }

        if ($missingFields) {
            $this->validationAggregateError->addError(
                __('The following menu required import fields are missing: "%1".', implode('", "', $missingFields))
            );
        }
    }

    private function validateStores(array $stores): void
    {
        $stores = array_unique($stores);
        $invalidStores = [];

        foreach ($stores as $store) {
            if (!$this->store->get($store)) {
                $invalidStores[] = $store;
            }
        }

        if ($invalidStores) {
            $this->validationAggregateError->addError(
                __('The following store codes/IDs are invalid: "%1".', implode('", "', $invalidStores))
            );
        }
    }
}
