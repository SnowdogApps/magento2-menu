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

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\Store;

class CmsPage extends AbstractNode
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param ResourceConnection $resource
     * @param MetadataPool $metadataPool
     */
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
     * @param array $pagesCodes
     * @return array
     * @throws \Exception
     */
    public function getPageIds($storeId, $pagesCodes = [])
    {
        $metadata = $this->metadataPool->getMetadata(PageInterface::class);
        $identifierField = $metadata->getIdentifierField();
        $linkField = $metadata->getLinkField();

        $connection = $this->getConnection('read');

        $pageTable = $this->getTable('cms_page');
        $storeTable = $this->getTable('cms_page_store');

        $select = $connection->select()->from(
            ['p' => $pageTable],
            [$identifierField, 'identifier']
        )->join(
            ['s' => $storeTable],
            'p.' . $linkField . ' = s.' . $linkField,
            []
        )->where(
            's.store_id IN (0, ?)',
            $storeId
        )->where(
            'p.identifier IN (?)',
            $pagesCodes
        )->where(
            'p.is_active = ?',
            1
        )->order(
            's.store_id ASC'
        );

        $codes = $connection->fetchAll($select);

        $pageIds = [];

        foreach ($codes as $row) {
            $pageIds[$row['identifier']] = $row[$identifierField];
        }

        return $pageIds;
    }
}
