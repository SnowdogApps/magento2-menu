<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\ImportExport\File\Download as FileDownload;
use Snowdog\Menu\Model\ImportExport\Import\SampleData;

class DownloadImportSample extends Action implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FileDownload
     */
    private $fileDownload;

    /**
     * @var SampleData
     */
    private $sampleData;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        FileDownload $fileDownload,
        SampleData $sampleData
    ) {
        $this->logger = $logger;
        $this->fileDownload = $fileDownload;
        $this->sampleData = $sampleData;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            return $this->fileDownload->generateDownloadFile(
                SampleData::DOWNLOAD_FILE_ID,
                $this->sampleData->get()
            );
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(
                __('An error occurred while generating menu import sample file.')
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/import');
    }
}
