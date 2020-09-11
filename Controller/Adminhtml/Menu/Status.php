<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\Menu;
use Snowdog\Menu\Model\MenuFactory;

class Status extends Action
{
    public const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /** @var MenuRepositoryInterface */
    private $menuRepository;

    /** @var MenuFactory */
    private $menuFactory;

    /**
     * @param Action\Context $context
     * @param MenuRepositoryInterface $menuRepository
     * @param MenuFactory $menuFactory
     */
    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menuFactory
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->menuFactory = $menuFactory;
    }

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

    /**
     * Returns menu model based on the Request (requested with `id` or fresh instance)
     *
     * @return Menu
     */
    private function getCurrentMenu(): Menu
    {
        $menuId = $this->getRequest()->getParam('id');

        if ($menuId) {
            return $this->menuRepository->getById($menuId);
        }

        return $this->menuFactory->create();
    }
}
