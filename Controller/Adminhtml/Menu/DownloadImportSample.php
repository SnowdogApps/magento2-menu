<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\ImportExport\ExportFile;
use Snowdog\Menu\Model\ImportExport\Import\SampleFile;

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
     * @var ExportFile
     */
    private $exportFile;

    /**
     * @var SampleFile
     */
    private $sampleFile;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        ExportFile $exportFile,
        SampleFile $sampleFile
    ) {
        $this->logger = $logger;
        $this->exportFile = $exportFile;
        $this->sampleFile = $sampleFile;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            return $this->exportFile->generateDownloadFile(
                SampleFile::DOWNLOAD_FILE_ID,
                $this->sampleFile->getSampleData()
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(
                __('An error occurred while generating menu import sample file.')
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*');
    }
}
