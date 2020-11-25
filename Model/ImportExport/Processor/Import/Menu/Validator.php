<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Menu;

use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\Processor\Store;

class Validator
{
    const REQUIRED_FIELDS = [
        MenuInterface::TITLE,
        MenuInterface::IDENTIFIER,
        MenuInterface::CSS_CLASS,
        MenuInterface::IS_ACTIVE,
        ExtendedFields::STORES
    ];

    /**
     * @var Store
     */
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function validate(array $data)
    {
        $this->validateRequiredFields($data);
        $this->validateStores($data[ExtendedFields::STORES]);
    }

    /**
     * @throws ValidatorException
     */
    private function validateRequiredFields(array $data)
    {
        $missingFields = [];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if ($missingFields) {
            throw new ValidatorException(
                __('The following menu required import fields are missing: "%1".', implode('", "', $missingFields))
            );
        }
    }

    /**
     * @throws ValidatorException
     */
    private function validateStores(array $stores)
    {
        $invalidStores = [];

        foreach ($stores as $store) {
            if (!$this->store->get($store)) {
                $invalidStores[] = $store;
            }
        }

        if ($invalidStores) {
            throw new ValidatorException(
                __('The following store codes/IDs are invalid: "%1".', implode('", "', $invalidStores))
            );
        }
    }
}
