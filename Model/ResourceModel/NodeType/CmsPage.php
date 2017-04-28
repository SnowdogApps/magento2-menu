<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model\ResourceModel\NodeType;

use Magento\Catalog\Model\Category as CoreCategory;
use Magento\Store\Model\Store;

class CmsPage extends AbstractNode
{
    /**
     * @return array
     */
    public function fetchConfigData()
    {
        $connection = $this->getConnection('read');

        $select = $connection->select()->from(
            $connection->getTableName('cms_page'),
            ['title', 'identifier']
        );

        return $connection->fetchPairs($select);
    }

    /**
     * @param int   $storeId
     * @param array $pageIds
     *
     * @return array
     */
    public function fetchData($storeId = Store::DEFAULT_STORE_ID, $pageIds = [])
    {
        $connection = $this->getConnection('read');

        $table = $connection->getTableName('url_rewrite');

        $select = $connection
            ->select()
            ->from($table, ['entity_id', 'request_path'])
            ->where('entity_type = ?', 'cms-page')
            ->where('store_id = ?', $storeId)
            ->where('entity_id IN (?)', array_values($pageIds));

        return $connection->fetchPairs($select);
    }

    /**
     * @param int|string $storeId
     * @param array      $pagesCodes
     *
     * @return array
     */
    public function getPageIds($storeId, $pagesCodes = [])
    {
        $connection = $this->getConnection('read');

        $pageTable = $connection->getTableName('cms_page');
        $storeTable = $connection->getTableName('cms_page_store');

        $select = $connection->select()->from(
            ['p' => $pageTable],
            ['page_id', 'identifier']
        )->join(['s' => $storeTable], 'p.page_id = s.page_id', [])->where(
            's.store_id IN (0, ?)',
            $storeId
        )->where('p.identifier IN (?)', $pagesCodes)->where('p.is_active = ?', 1)->order('s.store_id ASC');

        $codes = $connection->fetchAll($select);

        $pageIds = [];

        foreach ($codes as $row) {
            $pageIds[$row['identifier']] = $row['page_id'];
        }

        return $pageIds;
    }
}