<?php
declare(strict_types=1);

namespace Snowdog\Menu\Model;

use Magento\Framework\Model\AbstractModel;
use Snowdog\Menu\Api\Data\NodeTranslationInterface;
use Snowdog\Menu\Model\ResourceModel\NodeTranslation as NodeTranslationResource;

class NodeTranslation extends AbstractModel implements NodeTranslationInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(NodeTranslationResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getTranslationId(): ?int
    {
        return $this->getData(self::TRANSLATION_ID) === null
            ? null
            : (int)$this->getData(self::TRANSLATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTranslationId(int $id): NodeTranslationInterface
    {
        return $this->setData(self::TRANSLATION_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getNodeId(): int
    {
        return (int)$this->getData(self::NODE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setNodeId(int $nodeId): NodeTranslationInterface
    {
        return $this->setData(self::NODE_ID, $nodeId);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId(): int
    {
        return (int)$this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId(int $storeId): NodeTranslationInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title): NodeTranslationInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): NodeTranslationInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $updatedAt): NodeTranslationInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function getValue(): string
    {
        return (string)$this->getData('value');
    }

    public function setValue(string $value): void
    {
        $this->setData('value', $value);
    }
}
