<?php

namespace Snowdog\Menu\Helper;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ProductMetadataInterface\Proxy as ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection\Proxy as ResourceConnection;

/**
 * Class EavStructureWrapper
 *
 * @package Snowdog\Menu\Helper
 */
class EavStructureWrapper
{
    /**
     * EavStructureWrapper Cache Tag
     */
    const SNOWDOG_MENU_DATABASE_ENTITY_ID_TAG = 'snowdog_menu_database_entity_id_tag';

    /**
     * EavStructureWrapper Cache Tag For Block
     */
    const SNOWDOG_MENU_DATABASE_BLOCK_ENTITY_ID_TAG = 'snowdog_menu_database_block_entity_id_tag';

    /**
     * EavStructureWrapper Cache Tag For Cms Page
     */
    const SNOWDOG_MENU_DATABASE_CMS_PAGE_ENTITY_ID_TAG = 'snowdog_menu_database_cms_page_entity_id_tag';

    /**
     * Enterprise Column Name
     */
    const ENTERPRISE_EDITION_COLUMN = 'row_id';

    /**
     * Community Column Name
     */
    const COMMUNITY_EDITION_COLUMN = 'entity_id';

    /**
     * Enterprise Block Column Index
     */
    const COMMUNITY_EDITION_BLOCK_COLUMN_INDEX = 'block_id';

    /**
     * Enterprise Cms Page Column Index
     */
    const COMMUNITY_EDITION_CMS_PAGE_COLUMN_INDEX = 'page_id';

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $columnName = '';

    /**
     * @var string
     */
    private $blockColumnName = '';

    /**
     * @var
     */
    private $cmsPageColumnName = '';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * EavStructureWrapper constructor.
     *
     * @param ProductMetadataInterface $productMetadata
     * @param ResourceConnection $connection
     * @param CacheInterface $cache
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ResourceConnection $connection,
        CacheInterface $cache
    ) {
        $this->productMetadata = $productMetadata;
        $this->connection = $connection;
        $this->cache = $cache;
        $this->initCacheVariables();
    }

    protected function initCacheVariables()
    {
        if ($columnName = $this->cache->load(self::SNOWDOG_MENU_DATABASE_ENTITY_ID_TAG)) {
            $this->columnName = $columnName;
        }
        if ($blockColumnName = $this->cache->load(self::SNOWDOG_MENU_DATABASE_BLOCK_ENTITY_ID_TAG)) {
            $this->blockColumnName = $blockColumnName;
        }
        if ($cmsPagecolumnName = $this->cache->load(self::SNOWDOG_MENU_DATABASE_CMS_PAGE_ENTITY_ID_TAG)) {
            $this->cmsPageColumnName = $cmsPagecolumnName;
        }
    }

    /**
     * @return string
     */
    public function getEdition()
    {
        return strtolower($this->productMetadata->getEdition());
    }

    /**
     * @param string|null $entity
     * @return string
     */
    private function getEntityColumnNameByVersion($entity = null)
    {
        if ($this->getEdition() == 'enterprise') {
            return self::ENTERPRISE_EDITION_COLUMN;
        }

        return $this->getCommunityColumn($entity);
    }
    /**
     * @return string
     */
    public function getEntityColumnName()
    {
        if ($this->columnName) {
            return $this->columnName;
        }

        $column = $this->getEntityColumnNameByVersion();
        try {
            $this->checkVersion($column);
        } catch (\Exception $e) {
            $column = self::COMMUNITY_EDITION_COLUMN;
        }
        $this->cache->save($column, self::SNOWDOG_MENU_DATABASE_ENTITY_ID_TAG);

        return $column;
    }

    /**
     * @return string
     */
    public function getEntityBlockColumnName()
    {
        if ($this->blockColumnName) {
            return $this->blockColumnName;
        }

        $column = $this->getEntityColumnNameByVersion('block');
        try {
            $this->checkVersion($column);
        } catch (\Exception $e) {
            $column = self::COMMUNITY_EDITION_BLOCK_COLUMN_INDEX;
        }
        $this->cache->save($column, self::SNOWDOG_MENU_DATABASE_BLOCK_ENTITY_ID_TAG);

        return $column;
    }

    /**
     * @return string
     */
    public function getCmsPageEntityColumnName()
    {
        if ($this->cmsPageColumnName) {
            return $this->cmsPageColumnName;
        }

        $column = $this->getEntityColumnNameByVersion('cms_page');
        try {
            $this->checkVersion($column);
        } catch (\Exception $e) {
            $column = self::COMMUNITY_EDITION_CMS_PAGE_COLUMN_INDEX;
        }
        $this->cache->save($column, self::SNOWDOG_MENU_DATABASE_CMS_PAGE_ENTITY_ID_TAG);

        return $column;
    }

    /**
     * @param string $column
     * @return string
     */
    protected function checkVersion($column)
    {
        $connection = $this->connection->getConnection();
        $select = $connection->select()
            ->from($this->connection->getTableName('catalog_product_entity_varchar'), $column);

        return $connection->fetchOne($select);
    }

    /**
     * @param $entity
     * @return string
     */
    private function getCommunityColumn($entity)
    {
        switch ($entity) {
            case 'block':
                return self::COMMUNITY_EDITION_BLOCK_COLUMN_INDEX;
            case 'cms_page':
                return self::COMMUNITY_EDITION_CMS_PAGE_COLUMN_INDEX;
            default:
                return self::COMMUNITY_EDITION_COLUMN;
        }
    }
}
