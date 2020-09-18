<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Snowdog\Menu\Controller\Adminhtml\AbstractMenu;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\MenuFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\Page;

/**
 * Class Edit
 */
class Edit extends AbstractMenu implements HttpGetActionInterface
{
    const REGISTRY_CODE = 'snowmenu_menu';

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var MenuFactory
     */
    private $menuFactory;

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
        $this->menuRepository = $menuRepository;
        $this->menuFactory = $menuFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct(
            $context
        );
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(): ResultInterface
    {
        $menuId = (int) $this->getRequest()->getParam(self::ID);

        if ($menuId) {
            try {
                $model = $this->menuRepository->getById($menuId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This menu no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/index');
            }
        } else {
            $model = $this->menuFactory->create();
        }

        $this->coreRegistry->register(self::REGISTRY_CODE, $model);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $menuId ? __('Edit Menu %1', $model->getTitle()) : __('New Menu'),
            $menuId ? __('Edit Menu %1', $model->getTitle()) : __('New Menu')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Menus'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Menu'));

        return $resultPage;
    }
}
