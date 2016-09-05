<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class Create extends Action
{

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        // TODO: Implement execute() method.
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Snowdog_Menu::menus');
    }
}