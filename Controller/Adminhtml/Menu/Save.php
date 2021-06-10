<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Controller\Adminhtml\MenuAction;
use Snowdog\Menu\Model\MenuFactory;
use Snowdog\Menu\Service\Menu\Cloner as MenuCloner;
use Snowdog\Menu\Service\Menu\Hydrator as MenuHydrator;
use Snowdog\Menu\Service\Menu\SaveRequestProcessor as MenuSaveRequestProcessor;

class Save extends MenuAction implements HttpPostActionInterface
{
    private const EDIT_RETURN_REDIRECTS = ['edit', 'continue', 'duplicate'];

    /**
     * @var MenuCloner
     */
    private $menuCloner;

    /**
     * @var MenuHydrator
     */
    private $hydrator;

    /**
     * @var MenuSaveRequestProcessor
     */
    private $menuSaveRequestProcessor;

    public function __construct(
        Context $context,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menuFactory,
        MenuCloner $menuCloner,
        MenuHydrator $hydrator,
        MenuSaveRequestProcessor $menuSaveRequestProcessor
    ) {
        $this->menuCloner = $menuCloner;
        $this->hydrator = $hydrator;
        $this->menuSaveRequestProcessor = $menuSaveRequestProcessor;

        parent::__construct($context, $menuRepository, $menuFactory);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $menu = $this->getCurrentMenu();
        $request = $this->getRequest();
        $nodes = $request->getParam('serialized_nodes');
        $nodes = $nodes ? json_decode($nodes, true) : [];

        $this->hydrator->mapRequest($menu, $request);
        $this->menuRepository->save($menu);
        $menu->saveStores($request->getParam('stores'));

        $this->menuSaveRequestProcessor->saveData($menu, $nodes);

        return $this->processRedirect($menu);
    }

    private function processRedirect(MenuInterface $menu): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirect = $this->getRequest()->getParam('back');

        $pathAction = '';
        $pathParams = [];

        if ($redirect === 'duplicate') {
            $menu = $this->menuCloner->clone($menu);
        }

        if (in_array($redirect, self::EDIT_RETURN_REDIRECTS)) {
            $pathAction = 'edit';
            $pathParams = [self::ID => $menu->getId(), '_current' => true];
        }

        return $resultRedirect->setPath("*/*/${pathAction}", $pathParams);
    }
}
