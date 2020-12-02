<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\ImportExport\FileUpload;
use Snowdog\Menu\Model\ImportExport\ImportProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;

class ImportPost extends Action implements HttpPostActionInterface
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
        FileUpload $fileUpload,
        ImportProcessor $importProcessor
    ) {
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->fileUpload = $fileUpload;
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
            $importData = $this->fileUpload->uploadFileAndGetData();
            $menu = $this->importProcessor->importData($importData);

            $this->messageManager->addSuccessMessage(__('Menu "%1" has been successfully imported.', $menu));

            return $resultRedirect->setPath('*/*');
        } catch (ValidationAggregateError $exception) {
            $exception->flush();
        } catch (ValidatorException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (Exception $exception) {
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
