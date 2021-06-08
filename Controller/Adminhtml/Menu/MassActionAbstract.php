<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Exception;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\ResourceModel\Menu\Collection;
use Snowdog\Menu\Model\ResourceModel\Menu\CollectionFactory;

abstract class MassActionAbstract extends Action
{
    public const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /** @var MenuRepositoryInterface */
    protected $menuRepository;
    private $filter;
    private $collectionFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        MenuRepositoryInterface $menuRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->menuRepository = $menuRepository;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->process($collection);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/index');

        return $redirect;
    }

    abstract protected function process(Collection $collection);
}
