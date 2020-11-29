<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\ImportExport\ExportFile;
use Snowdog\Menu\Model\ImportExport\ExportProcessor;

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
     * @var ExportFile
     */
    private $exportFile;

    /**
     * @var ExportProcessor
     */
    private $exportProcessor;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        ExportFile $exportFile,
        ExportProcessor $exportProcessor
    ) {
        $this->logger = $logger;
        $this->exportFile = $exportFile;
        $this->exportProcessor = $exportProcessor;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $menuId = $this->getRequest()->getParam('id');

        try {
            return $this->exportFile->generateDownloadFile(
                $menuId,
                $this->exportProcessor->getExportData((int) $menuId)
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(
                __('An error occurred while exporting menu %1.', $menuId)
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*');
    }
}
