<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Controller\Adminhtml\MenuAction;
use Snowdog\Menu\Model\MenuFactory;
use Snowdog\Menu\Service\Menu\CloneRequestProcessor;

class Duplicate extends MenuAction implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var CloneRequestProcessor
     */
    private $cloneRequestProcessor;

    public function __construct(
        Context $context,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menuFactory,
        CloneRequestProcessor $cloneRequestProcessor
    ) {
        $this->cloneRequestProcessor = $cloneRequestProcessor;
        parent::__construct($context, $menuRepository, $menuFactory);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $menu = $this->getCurrentMenu();
        $redirectPath = '*/*';

        if (!$menu->getId()) {
            $this->messageManager->addErrorMessage(__('Cannot duplicate a menu with an invalid ID.'));
            return $resultRedirect->setPath($redirectPath);
        }

        $this->cloneRequestProcessor->clone($menu);

        return $resultRedirect->setPath($redirectPath);
    }
}
