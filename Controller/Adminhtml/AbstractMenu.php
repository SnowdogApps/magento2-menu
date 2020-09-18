<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

abstract class AbstractMenu extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * Menu ID key
     */
    const ID = 'menu_id';

    /**
     * @param Page $resultPage
     * @return Page
     */
    protected function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu('Snowdog_Menu::menus')
            ->addBreadcrumb(__('Snowdog'), __('Snowdog'))
            ->addBreadcrumb(__('Menu'), __('Menu'));

        return $resultPage;
    }
}
