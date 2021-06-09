<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Snowdog\Menu\Api\MenuManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ImportCategories extends Action implements HttpPostActionInterface
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var MenuManagementInterface
     */
    private $menuManagement;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param MenuManagementInterface $menuManagement
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        MenuManagementInterface $menuManagement
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->menuManagement = $menuManagement;
        parent::__construct($context);
    }

    public function execute()
    {
        $categoryId = (int) $this->_request->getParam('category_id');
        $depth = $this->_request->getParam('depth') ?: null;

        $result = $this->resultJsonFactory->create();
        try {
            $categoryTree = $this->menuManagement->getCategoryNodeList($categoryId, $depth);
            $output = [
                'success' => 1,
                'list' => $categoryTree
            ];
        } catch (NoSuchEntityException $exception) {
            $output = [
                'success' => 0,
                'message' => $exception->getMessage(),
                'list' => []
            ];
        }
        $result->setData($output);

        return $result;
    }
}
