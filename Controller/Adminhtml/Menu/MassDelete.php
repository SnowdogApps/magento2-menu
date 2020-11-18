<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Ui\Component\MassAction\Filter;
use Snowdog\Menu\Model\ResourceModel\Menu\CollectionFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\Collection\AbstractDb;
use Exception;

/**
 * Class MassDelete
 *
 * This action deletes multiple menus
 */
class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param MenuRepositoryInterface $menuRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        MenuRepositoryInterface $menuRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->menuRepository = $menuRepository;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        $collection = $this->getMenuCollection();
        $collectionSize = $collection->getSize();

        foreach ($collection as $menu) {
            try {
                $this->menuRepository->delete($menu);
            } catch (CouldNotDeleteException $exception) {
                --$collectionSize;
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return AbstractDb
     * @throws LocalizedException
     */
    private function getMenuCollection(): AbstractDb
    {
        return $this->filter->getCollection($this->collectionFactory->create());
    }
}
