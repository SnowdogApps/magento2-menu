<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\ImportExport\ExportProcessor;

class Export extends Action
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
     * @var ExportProcessor
     */
    private $exportProcessor;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        ExportProcessor $exportProcessor
    ) {
        $this->logger = $logger;
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
            return $this->exportProcessor->getDownloadFile($menuId);
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
