<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\Menu\ImportProcessor;

class ImportPost extends Action
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImportProcessor
     */
    private $importProcessor;

    public function __construct(
        Action\Context $context,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        ImportProcessor $importProcessor
    ) {
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->importProcessor = $importProcessor;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $menu = $this->importProcessor->importCsv();
            $this->messageManager->addSuccessMessage(__('Menu "%1" has been successfully imported.', $menu));

            return $resultRedirect->setPath('*/*');
        } catch (ValidatorException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(__('An error occurred while importing menu.'));
        }

        $refererUrl = $this->_redirect->getRefererUrl();

        if ($refererUrl === $this->storeManager->getStore()->getBaseUrl()) {
            return $resultRedirect->setPath('*/*');
        }

        return $resultRedirect->setUrl($refererUrl);
    }
}
