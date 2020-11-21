<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\ImportExport\Import\SampleFile;

class DownloadImportSample extends Action
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
     * @var SampleFile
     */
    private $sampleFile;

    public function __construct(Action\Context $context, LoggerInterface $logger, SampleFile $sampleFile)
    {
        $this->logger = $logger;
        $this->sampleFile = $sampleFile;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            return $this->sampleFile->getDownloadFile();
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
