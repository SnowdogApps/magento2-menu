<?php
declare(strict_types=1);

namespace Snowdog\Menu\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\NodeTranslationInterface;

interface NodeTranslationRepositoryInterface
{
    /**
     * Save node translation
     *
     * @param NodeTranslationInterface $translation
     * @return NodeTranslationInterface
     * @throws CouldNotSaveException
     */
    public function save(NodeTranslationInterface $translation): NodeTranslationInterface;

    /**
     * Get node translation by ID
     *
     * @param int $translationId
     * @return NodeTranslationInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $translationId): NodeTranslationInterface;

    /**
     * Get node translation by node ID and store ID
     *
     * @param int $nodeId
     * @param int $storeId
     * @return NodeTranslationInterface
     * @throws NoSuchEntityException
     */
    public function getByNodeAndStore(int $nodeId, int $storeId): NodeTranslationInterface;

    /**
     * Get all translations for multiple nodes in a specific store
     *
     * @param array $nodeIds
     * @param int $storeId
     * @return NodeTranslationInterface[]
     */
    public function getByNodeIds(array $nodeIds, int $storeId): array;

    /**
     * Get all translations for a node
     *
     * @param int $nodeId
     * @return NodeTranslationInterface[]
     */
    public function getByNodeId(int $nodeId): array;

    /**
     * Delete node translation
     *
     * @param NodeTranslationInterface $translation
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(NodeTranslationInterface $translation): bool;

    /**
     * Delete node translation by ID
     *
     * @param int $translationId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $translationId): bool;

    /**
     * Delete all translations for a node
     *
     * @param int $nodeId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteByNodeId(int $nodeId): bool;
}
