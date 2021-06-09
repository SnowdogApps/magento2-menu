<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\ImportExport\File\Download as FileDownload;
use Snowdog\Menu\Model\ImportExport\Processor\Export as ExportProcessor;

class Export extends Action implements HttpGetActionInterface
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
     * @var ExportProcessor
     */
    private $exportProcessor;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        FileDownload $fileDownload,
        ExportProcessor $exportProcessor
    ) {
        $this->logger = $logger;
        $this->fileDownload = $fileDownload;
        $this->exportProcessor = $exportProcessor;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $menuId = $this->getRequest()->getParam('menu_id');

        try {
            return $this->fileDownload->generateDownloadFile(
                $menuId,
                $this->exportProcessor->getExportData((int) $menuId)
            );
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(
                __('An error occurred while exporting menu %1.', $menuId)
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*');
    }
}
