<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory as HttpFileFactory;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\ImportExport\ExportProcessor;

class Export extends Action
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    const FILE_NAME = 'menu-{menu_id}.yaml';

    /**
     * @var HttpFileFactory
     */
    private $httpFileFactory;

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
        HttpFileFactory $httpFileFactory,
        LoggerInterface $logger,
        ExportProcessor $exportProcessor
    ) {
        $this->httpFileFactory = $httpFileFactory;
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
            return $this->httpFileFactory->create(
                strtr(self::FILE_NAME, ['{menu_id}' => $menuId]),
                $this->exportProcessor->getExportFileDownloadContent($menuId),
                DirectoryList::VAR_DIR
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
