<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Controller\Adminhtml\MenuAction;
use Snowdog\Menu\Model\MenuFactory;
use Snowdog\Menu\Service\Menu\Cloner as MenuCloner;

class Duplicate extends MenuAction implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MenuCloner
     */
    private $menuCloner;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menuFactory,
        MenuCloner $menuCloner
    ) {
        $this->logger = $logger;
        $this->menuCloner = $menuCloner;

        parent::__construct($context, $menuRepository, $menuFactory);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $menu = $this->getCurrentMenu();

        if (!$menu->getId()) {
            $this->messageManager->addErrorMessage(__('Invalid menu ID.'));
            return $resultRedirect->setPath('*/*');
        }

        try {
            $menuClone = $this->menuCloner->clone($menu);

            $successMessage = __(
                'Menu "%1" has been successfully duplicated as "%2".',
                $menu->getIdentifier(),
                $menuClone->getIdentifier()
            );

            $this->messageManager->addSuccessMessage($successMessage);
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(
                __('An error occurred while duplicating menu "%1".', $menu->getIdentifier())
            );
        }

        return $resultRedirect->setPath('*/*');
    }
}
