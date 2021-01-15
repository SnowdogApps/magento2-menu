<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Framework\App\ResponseInterface;
use Snowdog\Menu\Controller\Adminhtml\MenuAction;

class Status extends MenuAction
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute()
    {
        $menu = $this->getCurrentMenu();

        $menu->setIsActive((int) $this->getRequest()->getParam('is_active'));
        $this->menuRepository->save($menu);

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/index');

        return $redirect;
    }
}
