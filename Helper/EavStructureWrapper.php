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
     * Community Column Name
     */
    const COMMUNITY_EDITION_COLUMN = 'entity_id';

    /**
     * Enterprise Column Name
     */
    const ENTERPRISE_EDITION_COLUMN = 'row_id';

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
        if ($columnName = $this->cache->load(self::SNOWDOG_MENU_DATABASE_ENTITY_ID_TAG)) {
            $this->columnName = $columnName;
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
     * @return string
     */
    private function getEntityColumnNameByVersion()
    {
        if ($this->getEdition() == 'enterprise') {
            return self::ENTERPRISE_EDITION_COLUMN;
        }

        return self::COMMUNITY_EDITION_COLUMN;
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
        $connection = $this->connection->getConnection();
        try {
            $select = $connection->select()
                ->from($this->connection->getTableName('catalog_product_entity_varchar'), $column);
            $connection->fetchOne($select);
        } catch (\Exception $e) {
            $column = self::COMMUNITY_EDITION_COLUMN;
        }
        $this->cache->save($column, self::SNOWDOG_MENU_DATABASE_ENTITY_ID_TAG);

        return $column;
    }
}
