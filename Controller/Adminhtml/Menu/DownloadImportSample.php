<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory as HttpFileFactory;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\Menu\Import\SampleFile as ImportSampleFile;

class DownloadImportSample extends Action
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    const FILE_NAME = 'menu-sample.csv';

    /**
     * @var HttpFileFactory
     */
    private $httpFileFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImportSampleFile
     */
    private $importSampleFile;

    public function __construct(
        Action\Context $context,
        HttpFileFactory $httpFileFactory,
        LoggerInterface $logger,
        ImportSampleFile $importSampleFile
    ) {
        $this->httpFileFactory = $httpFileFactory;
        $this->logger = $logger;
        $this->importSampleFile = $importSampleFile;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            return $this->httpFileFactory->create(
                self::FILE_NAME,
                $this->importSampleFile->getFileDownloadContent(),
                DirectoryList::VAR_DIR
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
