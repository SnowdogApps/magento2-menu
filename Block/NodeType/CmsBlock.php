<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
use Magento\Store\Model\StoreManagerInterface;
use Snowdog\Menu\Api\NodeTypeInterface;

class CmsBlock extends Template implements NodeTypeInterface
{
    protected $nodes;
    protected $content;
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var Profiler
     */
    private $profiler;

    protected $_template = 'menu/node_type/cms_block.phtml';
    /**
     * @var FilterProvider
     */
    private $filterProvider;

    public function __construct(
        Context $context,
        ResourceConnection $connection,
        Profiler $profiler,
        FilterProvider $filterProvider,
        $data = []
    ) {
        $this->connection = $connection;
        $this->profiler = $profiler;
        parent::__construct($context, $data);
        $this->filterProvider = $filterProvider;
    }

    public function getJsonConfig()
    {
        $this->profiler->start(__METHOD__);
        $connection = $this->connection->getConnection('read');
        $select = $connection->select()->from(
            $this->connection->getTableName('cms_block'),
            ['title', 'identifier']
        );
        $options = $connection->fetchPairs($select);

        $data = [
            'snowMenuAutoCompleteField' => [
                'type'    => 'cms_block',
                'options' => $options,
                'message' => __('CMS Block not found'),
            ],
        ];
        $this->profiler->stop(__METHOD__);
        return json_encode($data);
    }

    public function fetchData(array $nodes)
    {
        $this->profiler->start(__METHOD__);
        $localNodes = [];
        $blocksCodes = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $blocksCodes[] = $node->getContent();
        }
        $this->nodes = $localNodes;

        $blockTable = $this->connection->getTableName('cms_block');
        $storeTable = $this->connection->getTableName('cms_block_store');
        $select = $this->connection->getConnection('read')->select()->from(
            ['p' => $blockTable],
            ['content', 'identifier']
        )->join(['s' => $storeTable], 'p.block_id = s.block_id', [])->where(
            's.store_id IN (0, ?)',
            $this->_storeManager->getStore()->getId()
        )->where('p.identifier IN (?)', $blocksCodes)->where('p.is_active = ?', 1)->order('s.store_id ASC');
        $codes = $this->connection->getConnection('read')->fetchAll($select);
        $this->content = [];
        foreach ($codes as $row) {
            $this->content[$row['identifier']] = $row['content'];
        }

        $this->profiler->stop(__METHOD__);
    }

    public function getHtml(int $nodeId, int $level)
    {
        $node = $this->nodes[$nodeId];
        $storeId = $this->_storeManager->getStore()->getId();
        if (isset($this->content[$node->getContent()])) {
            $content = $this->content[$node->getContent()];
            $content = $this->filterProvider->getBlockFilter()->setStoreId($storeId)->filter($content);
            return $content;
        }
        return '';
    }

    public function getAddButtonLabel()
    {
        return __("Add Cms Block node");
    }
}
