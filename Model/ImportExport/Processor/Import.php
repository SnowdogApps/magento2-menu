<?php

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\ImportSource;

class Import
{
    /**
     * @var ImportSource
     */
    private $importSource;

    /**
     * @var Import\Menu
     */
    private $menuProcessor;

    /**
     * @var Import\Node
     */
    private $nodeProcessor;

    public function __construct(
        ImportSource $importSource,
        Import\Menu $menuProcessor,
        Import\Node $nodeProcessor
    ) {
        $this->importSource = $importSource;
        $this->menuProcessor = $menuProcessor;
        $this->nodeProcessor = $nodeProcessor;
    }

    /**
     * @return string
     */
    public function importFile()
    {
        $data = $this->uploadFileAndGetData();
        $menu = $this->createMenu($data);

        if (isset($data[Export::NODES_FIELD])) {
            $this->nodeProcessor->createNodes($data[Export::NODES_FIELD], $menu->getId());
        }

        return $menu->getIdentifier();
    }

    /**
     * @return MenuInterface
     */
    private function createMenu(array $data)
    {
        $stores = $data[Export::STORES_FIELD];
        unset($data[Export::STORES_FIELD], $data[Export::NODES_FIELD]);

        return $this->menuProcessor->createMenu($data, $stores);
    }

    /**
     * @throws ValidatorException
     * @return array
     */
    private function uploadFileAndGetData()
    {
        try {
            $data = $this->importSource->uploadFileAndGetData();
        } catch (LocalizedException $exception) {
            throw new ValidatorException(__($exception->getMessage()));
        }

        $this->menuProcessor->validateImportData($data);

        if (isset($data[Export::NODES_FIELD])) {
            $this->nodeProcessor->validateImportData($data[Export::NODES_FIELD]);
        }

        return $data;
    }
}
