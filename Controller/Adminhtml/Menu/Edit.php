<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Snowdog\Menu\Api\MenuRepositoryInterface;

class Edit extends Action
{
    const REGISTRY_CODE = 'snowmenu_menu';
    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Action\Context $context, MenuRepositoryInterface $menuRepository, Registry $registry)
    {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->registry = $registry;
    }


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $model = $this->menuRepository->getById($id);
            $this->registry->register(self::REGISTRY_CODE, $model);
            $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $result->setActiveMenu('Snowdog_Menu::menus');
            $result->getConfig()->getTitle()->prepend(__('Edit Menu %1', $model->getTitle()));
            return $result;
        } catch (NoSuchEntityException $e) {
            $result = $this->resultRedirectFactory->create();
            $result->setPath('*/*/index');
            return $result;
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Snowdog_Menu::menus');
    }
}