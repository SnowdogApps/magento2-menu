<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\ImportExport\Helper\Data as ImportExportHelper;

class Import extends Action implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var ImportExportHelper
     */
    private $importExportHelper;

    public function __construct(Context $context, ImportExportHelper $importExportHelper)
    {
        $this->importExportHelper = $importExportHelper;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->messageManager->addNoticeMessage($this->importExportHelper->getMaxUploadSizeMessage());

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Menu Import'));

        return $resultPage;
    }
}
