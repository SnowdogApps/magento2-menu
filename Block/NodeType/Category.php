<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
use Magento\Store\Model\StoreManagerInterface;
use Snowdog\Menu\Api\NodeTypeInterface;

class Category extends Template implements NodeTypeInterface
{
    protected $nodes;
    protected $categoryUrls;
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

    protected $_template = 'menu/node_type/category.phtml';

    public function __construct(
        Context $context,
        ResourceConnection $connection,
        StoreManagerInterface $storeManager,
        Profiler $profiler,
        $data = []
    ) {
        $this->connection = $connection;
        $this->storeManager = $storeManager;
        $this->profiler = $profiler;
        parent::__construct($context, $data);
    }


    public function fetchData(array $nodes)
    {
        $this->profiler->start(__METHOD__);
        $localNodes = [];
        $categoryIds = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $categoryIds[] = (int)$node->getContent();
        }
        $this->nodes = $localNodes;
        $table = $this->connection->getTableName('url_rewrite');
        $select = $this->connection->getConnection('read')
                                   ->select()
                                   ->from($table, ['entity_id', 'request_path'])
                                   ->where('entity_type = ?', 'category')
                                   ->where('store_id = ?', $this->storeManager->getStore()->getId())
                                   ->where('entity_id IN (' . implode(',', $categoryIds) . ')');
        $this->categoryUrls = $this->connection->getConnection('read')->fetchPairs($select);
        $this->profiler->stop(__METHOD__);
    }

    public function getHtml(int $nodeId, int $level)
    {
        $classes = $level == 0 ? 'level-top"' : '';
        $node = $this->nodes[$nodeId];
        $url = $this->storeManager->getStore()->getBaseUrl() . $this->categoryUrls[(int)$node->getContent()];
        $title = $node->getTitle();
        return <<<HTML
<a href="$url" class="$classes" role="menuitem"><span>$title</span></a>
HTML;
    }

    public function getAddButtonLabel()
    {
        return __("Add Category node");
    }
}