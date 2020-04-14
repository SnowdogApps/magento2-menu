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
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class CmsBlock extends AbstractNode
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        ResourceConnection $resource,
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($resource);
    }

    /**
     * @return array
     */
    public function fetchConfigData()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->blockRepository->getList($searchCriteria)->getItems();
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
