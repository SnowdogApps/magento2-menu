<?php

namespace Snowdog\Menu\Helper;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;

class EavStructureWrapper
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var string
     */
    private $cmsPageLinkField;

    /**
     * @var string
     */
    private $cmsPageIdentifierField;

    /**
     * @var string
     */
    private $cmsBlockLinkField;

    /**
     * @var string
     */
    private $categoryLinkField;

    /**
     * @var string
     */
    private $categoryIdentifierField;

    /**
     * @param MetadataPool $metadataPool
     */
    public function __construct(MetadataPool $metadataPool)
    {
        $this->metadataPool = $metadataPool;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCategoryLinkField()
    {
        if ($this->categoryLinkField === null) {
            $this->categoryLinkField = $this->getMetadata(CategoryInterface::class)->getLinkField();
        }

        return $this->categoryLinkField;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCategoryIdentifierField()
    {
        if ($this->categoryIdentifierField === null) {
            $this->categoryIdentifierField = $this->getMetadata(CategoryInterface::class)->getIdentifierField();
        }

        return $this->categoryIdentifierField;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCmsBlockLinkField()
    {
        if ($this->cmsBlockLinkField === null) {
            $this->cmsBlockLinkField = $this->getMetadata(BlockInterface::class)->getLinkField();
        }

        return $this->cmsBlockLinkField;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCmsPageLinkField()
    {
        if ($this->cmsPageLinkField === null) {
            $this->cmsPageLinkField = $this->getMetadata(PageInterface::class)->getLinkField();
        }

        return $this->cmsPageLinkField;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCmsPageIdentifierField()
    {
        if ($this->cmsPageIdentifierField === null) {
            $this->cmsPageIdentifierField = $this->getMetadata(PageInterface::class)->getIdentifierField();
        }

        return $this->cmsPageIdentifierField;
    }

    /**
     * @param string $entityType
     * @return EntityMetadataInterface
     * @throws \Exception
     */
    private function getMetadata($entityType)
    {
        return $this->metadataPool->getMetadata($entityType);
    }
}
