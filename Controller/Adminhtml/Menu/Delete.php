<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\Search\FilterGroupBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Snowdog\Menu\Controller\Adminhtml\MenuAction;
use Snowdog\Menu\Model\MenuFactory;

/**
 * Class Delete
 *
 * This action deletes single menu
 */
class Delete extends MenuAction
{
    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;

    /**
     * @var FilterGroupBuilderFactory
     */
    private $filterGroupBuilderFactory;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @param Action\Context $context
     * @param MenuRepositoryInterface $menuRepository
     * @param NodeRepositoryInterface $nodeRepository
     * @param FilterBuilderFactory $filterBuilderFactory
     * @param FilterGroupBuilderFactory $filterGroupBuilderFactory
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param MenuFactory $menuFactory
     */
    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        FilterBuilderFactory $filterBuilderFactory,
        FilterGroupBuilderFactory $filterGroupBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        MenuFactory $menuFactory
    ) {
        parent::__construct($context, $menuRepository, $menuFactory);
        $this->nodeRepository = $nodeRepository;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->filterGroupBuilderFactory = $filterGroupBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
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
            $menu = $this->getCurrentMenu();
            if (!$menu->getMenuId()) {
                throw new CouldNotDeleteException(__('Menu does not exist'));
            }

            $this->menuRepository->delete($menu);

            $filterBuilder = $this->filterBuilderFactory->create();
            $filter = $filterBuilder->setField('menu_id')
                ->setValue($menu->getMenuId())
                ->setConditionType('eq')
                ->create();

            $filterGroupBuilder = $this->filterGroupBuilderFactory->create();
            $filterGroup = $filterGroupBuilder->addFilter($filter)->create();

            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria = $searchCriteriaBuilder->setFilterGroups([$filterGroup])->create();

            $nodes = $this->nodeRepository->getList($searchCriteria);
            foreach ($nodes->getItems() as $node) {
                $this->nodeRepository->delete($node);
            }
            $this->messageManager->addSuccessMessage(__('Menu %1 and it\'s nodes removed', $menu->getTitle()));
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (CouldNotDeleteException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/index');

        return $redirect;
    }
}
