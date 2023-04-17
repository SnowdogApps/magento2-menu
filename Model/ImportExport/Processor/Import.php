<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Import\InvalidNodes as InvalidNodesProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Menu as MenuProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node as NodeProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationException;

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

    /**
     * @var InvalidNodesProcessor
     */
    private $invalidNodesProcessor;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        MenuProcessor $menuProcessor,
        NodeProcessor $nodeProcessor,
        ValidationAggregateError $validationAggregateError,
        InvalidNodesProcessor $invalidNodesProcessor,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->menuProcessor = $menuProcessor;
        $this->nodeProcessor = $nodeProcessor;
        $this->validationAggregateError = $validationAggregateError;
        $this->invalidNodesProcessor = $invalidNodesProcessor;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @throws ValidationAggregateError
     */
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
    private function validateData(array &$data): void
    {
        $this->menuProcessor->validateImportData($data);

        if (isset($data[ExtendedFields::NODES])) {
            $this->nodeProcessor->validateImportData($data[ExtendedFields::NODES]);
        }

        if (empty($this->scopeConfig->getValue('snowmenu/import/strip_invalid_nodes'))
            && $this->validationAggregateError->getErrors()
        ) {
            throw $this->validationAggregateError;
        }

        $this->checkExceptionTypes($this->validationAggregateError);
        $this->invalidNodesProcessor->process($data, $this->validationAggregateError);
    }

    /**
     * Rethrows $e if there's at least one error not matching ValidationException
     * @throws ValidationAggregateError
     */
    private function checkExceptionTypes(ValidationAggregateError $e)
    {
        foreach ($e->getErrors() as $error) {
            if (!($error instanceof ValidationException)) {
                throw $e;
            }
        }
    }
}
