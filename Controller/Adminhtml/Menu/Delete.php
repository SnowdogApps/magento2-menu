<?php
declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NotFoundException;
use Snowdog\Menu\Api\MenuRepositoryInterface;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
    }

    /**
     * Dispatch request
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        try {
            $id = $this->getRequestMenuId();
            $menu = $this->menuRepository->getById($id);
            $this->menuRepository->deleteById($id);

            $this->messageManager->addSuccessMessage(__("Menu %1 and it's nodes removed", $menu->getTitle()));
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/index');
        return $redirect;
    }

    /**
     * Returns Menu ID provided with the Request
     *
     * @return int
     */
    private function getRequestMenuId(): int
    {
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id) {
            throw new \InvalidArgumentException('The request does not contain Menu ID');
        }

        return $id;
    }
}
