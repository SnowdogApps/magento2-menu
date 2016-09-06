<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
use Magento\Store\Model\StoreManagerInterface;
use Snowdog\Menu\Api\NodeTypeInterface;

class CmsPage implements NodeTypeInterface
{
    protected $nodes;
    protected $pageUrls;
    protected $pageIds;
    /**
     * @var ResourceConnection
     */
    private $connection;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Profiler
     */
    private $profiler;

    public function __construct(ResourceConnection $connection, StoreManagerInterface $storeManager, Profiler $profiler)
    {
        $this->connection = $connection;
        $this->storeManager = $storeManager;
        $this->profiler = $profiler;
    }


    public function fetchData(array $nodes)
    {
        $this->profiler->start(__METHOD__);
        $localNodes = [];
        $pagesCodes = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $pagesCodes[] = $node->getContent();
        }
        $this->nodes = $localNodes;
        // TODO pages codes into pages Ids
        $pageTable = $this->connection->getTableName('cms_page');
        $storeTable = $this->connection->getTableName('cms_page_store');
        $select = $this->connection->getConnection('read')->select()->from(
            ['p' => $pageTable],
            ['page_id', 'identifier']
        )->join(['s' => $storeTable], 'p.page_id = s.page_id', [])->where(
            's.store_id IN (0, ?)',
            $this->storeManager->getStore()->getId()
        )->where('p.identifier IN (?)', $pagesCodes)->where('p.is_active = ?', 1)->order('s.store_id ASC');
        $codes = $this->connection->getConnection('read')->fetchAll($select);
        $this->pageIds = [];
        foreach ($codes as $row) {
            $this->pageIds[$row['identifier']] = $row['page_id'];
        }

        $table = $this->connection->getTableName('url_rewrite');
        $select = $this->connection->getConnection('read')
                                   ->select()
                                   ->from($table, ['entity_id', 'request_path'])
                                   ->where('entity_type = ?', 'cms-page')
                                   ->where('store_id = ?', $this->storeManager->getStore()->getId())
                                   ->where('entity_id IN (?)', array_values($this->pageIds));
        $this->pageUrls = $this->connection->getConnection('read')->fetchPairs($select);
        $this->profiler->stop(__METHOD__);
    }

    public function getHtml(int $nodeId, int $level)
    {
        $classes = $level == 0 ? 'level-top"' : '';
        $node = $this->nodes[$nodeId];
        $pageId = $this->pageIds[$node->getContent()];
        $url = $this->storeManager->getStore()->getBaseUrl() . $this->pageUrls[$pageId];
        $title = $node->getTitle();
        return <<<HTML
<a href="$url" class="$classes" role="menuitem"><span>$title</span></a>
HTML;
    }

    public function getAddButtonLabel()
    {
        return __("Add Cms Page link node");
    }

    public function toHtml()
    {
        return '';
    }
}