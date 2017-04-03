<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
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
     * @var Profiler
     */
    private $profiler;

    protected $_template = 'menu/node_type/category.phtml';

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
            ['a' => $this->connection->getTableName('eav_attribute')],
            ['attribute_id']
        )->join(
            ['t' => $this->connection->getTableName('eav_entity_type')],
            't.entity_type_id = a.entity_type_id',
            []
        )->where('t.entity_type_code = ?', \Magento\Catalog\Model\Category::ENTITY)->where(
            'a.attribute_code = ?',
            'name'
        );
        $nameAttributeId = $connection->fetchOne($select);
        $select = $connection->select()->from(
            ['e' => $this->connection->getTableName('catalog_category_entity')],
            ['entity_id' => 'e.entity_id', 'parent_id' => 'e.parent_id']
        )->join(
            ['v' => $this->connection->getTableName('catalog_category_entity_varchar')],
            'v.entity_id = e.entity_id AND v.store_id = 0 AND v.attribute_id = ' . $nameAttributeId,
            ['name' => 'v.value']
        )->where('e.level > 0')->order('e.level ASC')->order('e.position ASC');
        $data = $connection->fetchAll($select);

        $labels = [];

        foreach ($data as $row) {
            if (isset($labels[$row['parent_id']])) {
                $label = $labels[$row['parent_id']];
            } else {
                $label = [];
            }
            $label[] = $row['name'];
            $labels[$row['entity_id']] = $label;
        }

        $options = [];
        foreach ($labels as $id => $label) {
            $label = implode(' > ', $label);
            $options[$label] = $id;
        }

        $data = [
            'snowMenuAutoCompleteField' => [
                'type'    => 'category',
                'options' => $options,
                'message' => __('Category not found'),
            ],
        ];
        $this->profiler->stop(__METHOD__);
        return json_encode($data);
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
                                   ->where('redirect_type = ?', 0)
                                   ->where('store_id = ?', $this->_storeManager->getStore()->getId())
                                   ->where('entity_id IN (' . implode(',', $categoryIds) . ')');
        $this->categoryUrls = $this->connection->getConnection('read')->fetchPairs($select);
        $this->profiler->stop(__METHOD__);
    }

    /**
     * @param int $nodeId
     * @param int|null $storeId
     * @return string
     */
    public function getCategoryUrl(int $nodeId, $storeId = null)
    {
        $node = $this->nodes[$nodeId];
        if (isset($this->categoryUrls[(int) $node->getContent()])) {
            $url = $this->_storeManager->getStore($storeId)->getBaseUrl() . $this->categoryUrls[(int) $node->getContent()];
        } else {
            $url = $this->_storeManager->getStore($storeId)->getBaseUrl();
        }

        return $url;
    }

    public function getHtml(int $nodeId, int $level, $storeId = null)
    {
        $classes = $level == 0 ? 'level-top' : '';
        $node = $this->nodes[$nodeId];
        $url = $this->getCategoryUrl($nodeId, $storeId);
        $title = $node->getTitle();
        return <<<HTML
<a href="$url" class="$classes" role="menuitem"><span>$title</span></a>
HTML;
    }

    public function getAddButtonLabel()
    {
        return __("Add Category node");
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }
}
