<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Controller\Adminhtml\MenuAction;
use Snowdog\Menu\Model\MenuFactory;
use Snowdog\Menu\Service\Menu\CloneRequestProcessor;
use Snowdog\Menu\Service\Menu\Hydrator as MenuHydrator;
use Snowdog\Menu\Service\Menu\SaveRequestProcessor as MenuSaveRequestProcessor;

class Save extends MenuAction implements HttpPostActionInterface
{
    private const EDIT_RETURN_REDIRECTS = ['edit', 'continue', 'duplicate'];

    /**
     * @var CloneRequestProcessor
     */
    private $cloneRequestProcessor;

    /**
     * @var MenuHydrator
     */
    private $hydrator;

    /**
     * @var MenuSaveRequestProcessor
     */
    private $menuSaveRequestProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menuFactory,
        CloneRequestProcessor $cloneRequestProcessor,
        MenuHydrator $hydrator,
        MenuSaveRequestProcessor $menuSaveRequestProcessor,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->cloneRequestProcessor = $cloneRequestProcessor;
        $this->hydrator = $hydrator;
        $this->menuSaveRequestProcessor = $menuSaveRequestProcessor;
        $this->storeManager = $storeManager;
        $this->logger = $logger;

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

        $this->processSave($menu, $nodes);

        return $this->processRedirect($menu);
    }

    private function processRedirect(MenuInterface $menu): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirect = $this->getRequest()->getParam('back');

        $pathAction = '';
        $pathParams = [];

        if ($redirect === 'duplicate') {
            $menu = $this->cloneRequestProcessor->clone($menu);
        }

        if (in_array($redirect, self::EDIT_RETURN_REDIRECTS)) {
            $pathAction = 'edit';
            $pathParams = [self::ID => $menu->getId(), '_current' => true];
        }

        return $resultRedirect->setPath("*/*/{$pathAction}", $pathParams);
    }

    private function getStores(): array
    {
        $request = $this->getRequest();
        $stores = $request->getParam('stores');
        if ($this->storeManager->isSingleStoreMode() || $stores === null) {
            return [$this->storeManager->getStore()->getId()];
        }

        return $stores;
    }

    private function processSave(MenuInterface $menu, $nodes): void
    {
        try {
            $this->menuRepository->save($menu);
            $menu->saveStores($this->getStores());

            $this->menuSaveRequestProcessor->saveData($menu, $nodes);

            if (empty($this->messageManager->getMessages()->getErrors())) {
                $this->messageManager->addSuccessMessage(new Phrase('Menu has been saved successfully.'));
            }
        } catch (AlreadyExistsException|CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(new Phrase('Could not save Menu: %1', [$e->getMessage()]));
        } catch (Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(new Phrase('An error occurred while saving menu.'));
        }
    }
}
