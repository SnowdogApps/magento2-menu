<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Snowdog\Menu\Controller\Adminhtml\MenuAction;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\MenuFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\Page;

class Edit extends MenuAction implements HttpGetActionInterface
{
    const REGISTRY_CODE = 'snowmenu_menu';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param MenuRepositoryInterface $menuRepository
     * @param MenuFactory $menuFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menuFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $menuRepository, $menuFactory);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $menuId = (int) $this->getRequest()->getParam(self::ID);
        $menu = $this->getCurrentMenu();

        if ($menuId && !$menu->getMenuId()) {
            $this->messageManager->addErrorMessage(__('This menu no longer exists.'));
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/index');
        }

        $this->coreRegistry->register(self::REGISTRY_CODE, $menu);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $menuId ? __('Edit Menu %1', $menu->getTitle()) : __('New Menu'),
            $menuId ? __('Edit Menu %1', $menu->getTitle()) : __('New Menu')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Menus'));
        $resultPage->getConfig()->getTitle()->prepend($menu->getId() ? $menu->getTitle() : __('New Menu'));

        return $resultPage;
    }
}
