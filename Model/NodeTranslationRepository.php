<?php
declare(strict_types=1);

namespace Snowdog\Menu\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\NodeTranslationInterface;
use Snowdog\Menu\Api\Data\NodeTranslationInterfaceFactory;
use Snowdog\Menu\Api\NodeTranslationRepositoryInterface;
use Snowdog\Menu\Model\ResourceModel\NodeTranslation as NodeTranslationResource;
use Snowdog\Menu\Model\ResourceModel\NodeTranslation\Collection;
use Snowdog\Menu\Model\ResourceModel\NodeTranslation\CollectionFactory;

class NodeTranslationRepository implements NodeTranslationRepositoryInterface
{
    /**
     * @var NodeTranslationResource
     */
    private NodeTranslationResource $resource;

    /**
     * @var NodeTranslationInterfaceFactory
     */
    private NodeTranslationInterfaceFactory $translationFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private SearchResultsInterfaceFactory $searchResultsFactory;

    public function __construct(
        NodeTranslationResource $resource,
        NodeTranslationInterfaceFactory $translationFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->translationFactory = $translationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(NodeTranslationInterface $translation): NodeTranslationInterface
    {
        try {
            $this->resource->save($translation);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $translation;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $translationId): NodeTranslationInterface
    {
        $translation = $this->translationFactory->create();
        $this->resource->load($translation, $translationId);
        if (!$translation->getId()) {
            throw new NoSuchEntityException(__('Node Translation with id "%1" does not exist.', $translationId));
        }
        return $translation;
    }

    /**
     * @inheritDoc
     */
    public function getByNodeAndStore(int $nodeId, int $storeId): NodeTranslationInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('node_id', $nodeId);
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->setPageSize(1);

        $translation = $collection->getFirstItem();

        if (!$translation->getId()) {
            throw new NoSuchEntityException(
                __('Node translation for node ID "%1" and store ID "%2" does not exist.', $nodeId, $storeId)
            );
        }

        return $translation;
    }

    /**
     * @inheritDoc
     */
    public function getByNodeId(int $nodeId): array
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('node_id', $nodeId);

        return $collection->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getByNodeIds(array $nodeIds, int $storeId): array
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('node_id', ['in' => $nodeIds]);
        $collection->addFieldToFilter('store_id', $storeId);

        return $collection->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(NodeTranslationInterface $translation): bool
    {
        try {
            $this->resource->delete($translation);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $translationId): bool
    {
        return $this->delete($this->getById($translationId));
    }

    public function deleteByNodeId(int $nodeId): bool
    {
        try {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('node_id', $nodeId);
            foreach ($collection as $translation) {
                $this->delete($translation);
            }
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete node translations: %1', $e->getMessage()));
        }
        return true;
    }
}
