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
use Magento\Framework\App\ResourceConnection;
use Snowdog\Menu\Helper\EavStructureWrapper;
use Magento\Store\Model\Store;

class CmsPage extends AbstractNode
{
    /**
     * @var EavStructureWrapper
     */
    protected $eavStructureWrapper;

    /**
     * CmsPage constructor.
     *
     * @param ResourceConnection $resource
     * @param EavStructureWrapper $eavStructureWrapper
     */
    public function __construct(
        ResourceConnection $resource,
        EavStructureWrapper $eavStructureWrapper
    ) {
        $this->eavStructureWrapper = $eavStructureWrapper;
        parent::__construct($resource);
    }

    /**
     * @return array
     */
    public function fetchConfigData()
    {
        $connection = $this->getConnection('read');

        $select = $connection->select()->from(
            $this->getTable('cms_page'),
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
        $table = $this->getTable('url_rewrite');

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
        $eavColumnName = $this->eavStructureWrapper->getCmsPageEntityColumnName();
        $connection = $this->getConnection('read');

        $pageTable = $this->getTable('cms_page');
        $storeTable = $this->getTable('cms_page_store');

        $select = $connection->select()->from(
            ['p' => $pageTable],
            [$eavColumnName, 'identifier']
        )->join(['s' => $storeTable], 'p.' . $eavColumnName . ' = s.' . $eavColumnName, [])->where(
            's.store_id IN (0, ?)',
            $storeId
        )->where('p.identifier IN (?)', $pagesCodes)->where('p.is_active = ?', 1)->order('s.store_id ASC');

        $codes = $connection->fetchAll($select);

        $pageIds = [];

        foreach ($codes as $row) {
            $pageIds[$row['identifier']] = $row[$eavColumnName];
        }

        return $pageIds;
    }
}