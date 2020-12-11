<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\Menu\Node\Image;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;

class Node
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        NodeRepositoryInterface $nodeRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
        $this->nodeRepository = $nodeRepository;
    }

    public function updateNodeImage(int $nodeId, ?string $image): void
    {
        try {
            $node = $this->nodeRepository->getById($nodeId);
        } catch (NoSuchEntityException $exception) {
            // Normally, this error should never happen.
            // But if it somehow does happen, then there is possibly an issue on JS side that should be fixed.
            $this->logger->critical($exception);
            return;
        }

        try {
            $node->setImage($image);
            $this->nodeRepository->save($node);
        } catch (CouldNotSaveException $exception) {
            // Normally, this error should never happen.
            // But if it somehow does happen, then there is possibly an issue on server-side that should be fixed.
            $this->logger->critical($exception);
        }
    }

    public function getNodeListImages(array $nodeIds): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(NodeInterface::NODE_ID, $nodeIds, 'in')
            ->addFilter(NodeInterface::IMAGE, true, 'notnull')
            ->addFilter(NodeInterface::IMAGE, '', 'neq')
            ->create();

        $nodes = $this->nodeRepository->getList($searchCriteria)->getItems();

        $images = [];
        foreach ($nodes as $node) {
            $images[$node->getId()] = $node->getImage();
        }

        return $images;
    }
}
