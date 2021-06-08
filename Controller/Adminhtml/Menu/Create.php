<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Controller\Adminhtml\MenuAction;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use Snowdog\Menu\Model\MenuFactory;

class Create extends MenuAction implements HttpGetActionInterface
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param MenuRepositoryInterface $menuRepository
     * @param MenuFactory $menuFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menuFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $menuRepository, $menuFactory);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
