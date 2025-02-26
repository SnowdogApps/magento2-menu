<?php
declare(strict_types=1);

namespace Snowdog\Menu\Api\Data;

interface NodeTranslationInterface
{
    public const TRANSLATION_ID = 'translation_id';
    public const NODE_ID = 'node_id';
    public const STORE_ID = 'store_id';
    public const TITLE = 'title';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * @return int|null
     */
    public function getTranslationId(): ?int;

    /**
     * @param int $id
     * @return NodeTranslationInterface
     */
    public function setTranslationId(int $id): NodeTranslationInterface;

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
     * @param string|null $title
     * @return NodeTranslationInterface
     */
    public function setTitle(?string $title): NodeTranslationInterface;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return NodeTranslationInterface
     */
    public function setCreatedAt(string $createdAt): NodeTranslationInterface;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param string $updatedAt
     * @return NodeTranslationInterface
     */
    public function setUpdatedAt(string $updatedAt): NodeTranslationInterface;
}
