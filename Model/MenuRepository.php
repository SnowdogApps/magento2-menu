<?php
namespace Snowdog\Menu\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\MenuSearchResultsInterfaceFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\ResourceModel\Menu\Collection;
use Snowdog\Menu\Model\ResourceModel\Menu\CollectionFactory;

class MenuRepository implements MenuRepositoryInterface
{
    /** @var MenuFactory */
    protected $menuFactory;

    /** @var CollectionFactory */
    protected $collectionFactory;

    /** @var MenuSearchResultsInterfaceFactory */
    private $menuSearchResultsFactory;

    /** @var ResourceModel\Menu */
    private $menuResourceModel;

    /**
     * @param MenuFactory $menuFactory
     * @param CollectionFactory $menuCollectionFactory
     * @param MenuSearchResultsInterfaceFactory $menuSearchResults
     * @param ResourceModel\Menu|null $menuResourceModel
     */
    public function __construct(
        MenuFactory $menuFactory,
        CollectionFactory $menuCollectionFactory,
        MenuSearchResultsInterfaceFactory $menuSearchResults,
        ResourceModel\Menu $menuResourceModel = null
    ) {
        $this->menuFactory = $menuFactory;
        $this->collectionFactory = $menuCollectionFactory;
        $this->menuSearchResultsFactory = $menuSearchResults;
        $this->menuResourceModel = $menuResourceModel
            ?? ObjectManager::getInstance()->get(ResourceModel\Menu::class); // Backwards-compatible class loader
    }

    /**
     * @inheritDoc
     */
    public function save(MenuInterface $menu)
    {
        try {
            $this->menuResourceModel->save($menu);
        } catch (\Exception $e) {
            throw new CouldNotSaveException($e->getMessage());
        }
        return $menu;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        $menuModel = $this->menuFactory->create();
        $this->menuResourceModel->load($menuModel, $id);

        if (!$menuModel->getId()) {
            throw new NoSuchEntityException(
                __('Menu with ID "%1" does not exist.', $id)
            );
        }

        return $menuModel;
    }

    /**
     * @inheritDoc
     */
    public function delete(MenuInterface $menu)
    {
        try {
            $this->menuResourceModel->delete($menu);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->menuSearchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        $isStoreFilterUsed = false;

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];

            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];

                if (!$isStoreFilterUsed && $filter->getField() === MenuInterface::STORE_ID) {
                    $isStoreFilterUsed = true;
                }
            }

            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }

        if ($isStoreFilterUsed) {
            $collection->joinStoreRelationTable();
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();

        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }

        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $objects = [];

        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }

        $searchResults->setItems($objects);

        return $searchResults;
    }

    public function get($identifier, $storeId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFilter('identifier', $identifier);
        $collection->addFilter('is_active', 1);
        $collection->join(['stores' => 'snowmenu_store'], 'main_table.menu_id = stores.menu_id', 'store_id');
        $collection->addFilter('store_id', $storeId);
        return $collection->getFirstItem();
    }

    public function setIsActiveByIds($ids, $isActive)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $connection = $collection->getConnection();
        $connection->update(
            'snowmenu_menu',
            ['is_active' => $isActive],
            ['menu_id IN (?)' => $ids]
        );
    }
}
