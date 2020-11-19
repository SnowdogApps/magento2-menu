<?php

namespace Snowdog\Menu\Model\ImportExport;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\MenuInterface;

class ImportProcessor
{
    /**
     * @var ImportSourceFactory
     */
    private $importSourceFactory;

    /**
     * @var ImportProcessor\Menu
     */
    private $menuImportProcessor;

    /**
     * @var ImportProcessor\Node
     */
    private $nodeImportProcessor;

    public function __construct(
        ImportSourceFactory $importSourceFactory,
        ImportProcessor\Menu $menuImportProcessor,
        ImportProcessor\Node $nodeImportProcessor
    ) {
        $this->importSourceFactory = $importSourceFactory;
        $this->menuImportProcessor = $menuImportProcessor;
        $this->nodeImportProcessor = $nodeImportProcessor;
    }

    /**
     * @return string
     */
    public function importFile()
    {
        $data = $this->uploadFileAndGetData();
        $menu = $this->createMenu($data);

        $this->nodeImportProcessor->createNodes($data[ExportProcessor::NODES_FIELD], $menu->getId());

        return $menu->getIdentifier();
    }

    /**
     * @return MenuInterface
     */
    private function createMenu(array $data)
    {
        $stores = $data[ExportProcessor::STORES_FIELD];
        unset($data[ExportProcessor::STORES_FIELD], $data[ExportProcessor::NODES_FIELD]);

        return $this->menuImportProcessor->createMenu($data, $stores);
    }

    /**
     * @throws ValidatorException
     * @return array
     */
    private function uploadFileAndGetData()
    {
        try {
            $importSource = $this->importSourceFactory->create();
            $data = $importSource->uploadFileAndGetData();
        } catch (LocalizedException $exception) {
            throw new ValidatorException(__($exception->getMessage()));
        }

        $this->menuImportProcessor->validateImportData($data);

        if (isset($data[ExportProcessor::NODES_FIELD])) {
            $this->nodeImportProcessor->validateImportData($data[ExportProcessor::NODES_FIELD]);
        }

        return $data;
    }
}
