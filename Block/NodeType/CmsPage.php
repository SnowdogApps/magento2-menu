<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
use Magento\Store\Model\StoreManagerInterface;
use Snowdog\Menu\Api\NodeTypeInterface;

class CmsPage extends Template implements NodeTypeInterface
{
    protected $nodes;
    protected $pageUrls;
    protected $pageIds;
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var Profiler
     */
    private $profiler;

    protected $_template = 'menu/node_type/cms_page.phtml';

    public function __construct(
        Context $context,
        ResourceConnection $connection,
        Profiler $profiler,
        $data = []
    ) {
        $this->connection = $connection;
        $this->profiler = $profiler;
        parent::__construct($context, $data);
    }

    public function getJsonConfig()
    {
        $this->profiler->start(__METHOD__);
        $connection = $this->connection->getConnection('read');
        $select = $connection->select()->from(
            $this->connection->getTableName('cms_page'),
            ['title', 'identifier']
        );
        $options = $connection->fetchPairs($select);

        $data = [
            'snowMenuAutoCompleteField' => [
                'type'    => 'cms_page',
                'options' => $options,
                'message' => __('CMS Page not found'),
            ],
        ];
        $this->profiler->stop(__METHOD__);
        return json_encode($data);
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

        $pageTable = $this->connection->getTableName('cms_page');
        $storeTable = $this->connection->getTableName('cms_page_store');
        $select = $this->connection->getConnection('read')->select()->from(
            ['p' => $pageTable],
            ['page_id', 'identifier']
        )->join(['s' => $storeTable], 'p.page_id = s.page_id', [])->where(
            's.store_id IN (0, ?)',
            $this->_storeManager->getStore()->getId()
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
                                   ->where('store_id = ?', $this->_storeManager->getStore()->getId())
                                   ->where('entity_id IN (?)', array_values($this->pageIds));
        $this->pageUrls = $this->connection->getConnection('read')->fetchPairs($select);
        $this->profiler->stop(__METHOD__);
    }

    public function getHtml(int $nodeId, int $level)
    {
        $classes = $level == 0 ? 'level-top' : '';
        $node = $this->nodes[$nodeId];
        if(isset($this->pageIds[$node->getContent()])) {
            $pageId = $this->pageIds[$node->getContent()];
            $url = $this->_storeManager->getStore()->getBaseUrl() . $this->pageUrls[$pageId];
        } else {
            $url = $this->_storeManager->getStore()->getBaseUrl();
        }
        $title = $node->getTitle();
        return <<<HTML
<a href="$url" class="$classes" role="menuitem"><span>$title</span></a>
HTML;
    }

    public function getAddButtonLabel()
    {
        return __("Add Cms Page link node");
    }

    public function initTemplate()
    {
        return $this->setTemplate($this->_template);
    }
}
