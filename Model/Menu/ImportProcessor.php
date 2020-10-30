<?php

namespace Snowdog\Menu\Model\Menu;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Model\ImportFactory;
use Snowdog\Menu\Model\Menu\ImportProcessor\Menu as MenuImportProcessor;
use Snowdog\Menu\Model\Menu\ImportProcessor\Node as NodeImportProcessor;

class ImportProcessor
{
    /**
     * @var ImportFactory
     */
    private $importFactory;

    /**
     * @var MenuImportProcessor
     */
    private $menuImportProcessor;

    /**
     * @var NodeImportProcessor
     */
    private $nodeImportProcessor;

    public function __construct(
        ImportFactory $importFactory,
        MenuImportProcessor $menuImportProcessor,
        NodeImportProcessor $nodeImportProcessor
    ) {
        $this->importFactory = $importFactory;
        $this->menuImportProcessor = $menuImportProcessor;
        $this->nodeImportProcessor = $nodeImportProcessor;
    }

    /**
     * @return string
     */
    public function importCsv()
    {
        $data = $this->uploadFileAndGetData();
        $menu = $this->createMenu($data);

        $this->nodeImportProcessor->createNodes($data[ExportProcessor::NODES_CSV_FIELD], $menu->getId());

        return $menu->getIdentifier();
    }

    /**
     * @return \Snowdog\Menu\Api\Data\MenuInterface
     */
    private function createMenu(array $data)
    {
        $stores = $data[ExportProcessor::STORES_CSV_FIELD];
        unset($data[ExportProcessor::STORES_CSV_FIELD], $data[ExportProcessor::NODES_CSV_FIELD]);

        return $this->menuImportProcessor->createMenu($data, $stores);
    }

    /**
     * @throws ValidatorException
     * @return array
     */
    private function uploadFileAndGetData()
    {
        $import = $this->importFactory->create();

        try {
            $source = $import->uploadFileAndGetSource();
        } catch (LocalizedException $exception) {
            throw new ValidatorException(__($exception->getMessage()));
        }

        $source->rewind();
        $data = $source->current();

        if (isset($data[ExportProcessor::NODES_CSV_FIELD])) {
            $data[ExportProcessor::NODES_CSV_FIELD] = $this->nodeImportProcessor->getNodesJsonData(
                $data[ExportProcessor::NODES_CSV_FIELD]
            );

            $this->nodeImportProcessor->validateImportData($data[ExportProcessor::NODES_CSV_FIELD]);
        }

        $this->menuImportProcessor->validateImportData($data);
        $import->deleteImportFile();

        $data[ExportProcessor::STORES_CSV_FIELD] = explode(',', $data[ExportProcessor::STORES_CSV_FIELD]);

        return $data;
    }
}
