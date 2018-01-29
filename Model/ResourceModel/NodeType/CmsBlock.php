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

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\Store;

class CmsBlock extends AbstractNode
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        ResourceConnection $resource,
        MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($resource);
    }

    /**
     * @return array
     */
    public function fetchConfigData()
    {
        $connection = $this->getConnection('read');

        $select = $connection->select()->from(
            $this->getTable('cms_block'),
            ['title', 'identifier']
        );

        return $connection->fetchPairs($select);
    }

    /**
     * @param int $storeId
     * @param array $blocksCodes
     * @return array
     * @throws \Exception
     */
    public function fetchData($storeId = Store::DEFAULT_STORE_ID, $blocksCodes = [])
    {
        $linkField = $this->metadataPool->getMetadata(BlockInterface::class)->getLinkField();
        $connection = $this->getConnection('read');

        $blockTable = $this->getTable('cms_block');
        $storeTable = $this->getTable('cms_block_store');

        $select = $connection->select()->from(
            ['p' => $blockTable],
            ['content', 'identifier']
        )->join(['s' => $storeTable], 'p.' . $linkField . ' = s.' .$linkField, [])->where(
            's.store_id IN (0, ?)',
            $storeId
        )->where('p.identifier IN (?)', $blocksCodes)->where('p.is_active = ?', 1)->order('s.store_id ASC');

        return $connection->fetchAll($select);
    }
}