<?php
declare(strict_types=1);

namespace Snowdog\Menu\Api\Data;

interface NodeTranslationInterface
{
    public const NODE_ID = 'node_id';
    public const STORE_ID = 'store_id';
    public const TITLE = 'title';

    /**
     * @return int
     */
    public function getNodeId(): int;

    /**
     * @param int $nodeId
     * @return NodeTranslationInterface
     */
    public function setNodeId(int $nodeId): NodeTranslationInterface;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     * @return NodeTranslationInterface
     */
    public function setStoreId(int $storeId): NodeTranslationInterface;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @param string $title
     * @return NodeTranslationInterface
     */
    public function setTitle(string $title): NodeTranslationInterface;
}
