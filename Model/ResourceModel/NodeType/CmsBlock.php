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
use Magento\Store\Model\Store;
use Snowdog\Menu\Helper\EavStructureWrapper;

class CmsBlock extends AbstractNode
{
    /**
     * @var EavStructureWrapper
     */
    protected $eavStructureWrapper;

    public function __construct(
        ResourceConnection $resource,
        EavStructureWrapper  $eavStructureWrapper
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
            $connection->getTableName('cms_block'),
            ['title', 'identifier']
        );

        return $connection->fetchPairs($select);
    }

    /**
     * @param int   $storeId
     * @param array $blocksCodes
     *
     * @return array
     */
    public function fetchData($storeId = Store::DEFAULT_STORE_ID, $blocksCodes = [])
    {
        $eavColumnName = $this->eavStructureWrapper->getEntityBlockColumnName();
        $connection = $this->getConnection('read');

        $blockTable = $connection->getTableName('cms_block');
        $storeTable = $connection->getTableName('cms_block_store');

        $select = $connection->select()->from(
            ['p' => $blockTable],
            ['content', 'identifier']
        )->join(['s' => $storeTable], 'p.' . $eavColumnName . ' = s.' .$eavColumnName, [])->where(
            's.store_id IN (0, ?)',
            $storeId
        )->where('p.identifier IN (?)', $blocksCodes)->where('p.is_active = ?', 1)->order('s.store_id ASC');

        return $connection->fetchAll($select);
    }
}