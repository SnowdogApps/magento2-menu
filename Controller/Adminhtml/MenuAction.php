<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\MenuFactory;

abstract class MenuAction extends Action
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
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    public function __construct(
        Context $context,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menuFactory
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->menuFactory = $menuFactory;
    }

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

    /**
     * Returns menu model based on the Request (requested with `menu_id` or fresh instance)
     *
     * @return MenuInterface
     */
    protected function getCurrentMenu(): MenuInterface
    {
        $menuId = (int) $this->getRequest()->getParam(self::ID);

        if ($menuId) {
            try {
                return $this->menuRepository->getById($menuId);
            } catch (NoSuchEntityException $exception) {
                return $this->menuFactory->create();
            }
        }

        return $this->menuFactory->create();
    }
}
