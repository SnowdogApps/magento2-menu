<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
use Magento\Store\Model\StoreManagerInterface;
use Snowdog\Menu\Api\NodeTypeInterface;
use Snowdog\Menu\Model\NodeType\CmsPage as CmsPageModel;

class CmsPage extends AbstractNode
{
    /**
     * @var string
     */
    protected $nodeType = 'cms-page';
    /**
     * @var array
     */
    protected $nodes;
    /**
     * @var array
     */
    protected $pageUrls;
    /**
     * @var array
     */
    protected $pageIds;
    /**
     * @var string
     */
    protected $_template = 'menu/node_type/cms_page.phtml';
    /**
     * @var CmsPageModel
     */
    private $_cmsPageModel;

    /**
     * CmsPage constructor.
     *
     * @param Context      $context
     * @param CmsPageModel $cmsPageModel
     * @param array        $data
     */
    public function __construct(
        Context $context,
        CmsPageModel $cmsPageModel,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->_cmsPageModel = $cmsPageModel;
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $data = $this->_cmsPageModel->fetchConfigData();

        return json_encode($data);
    }

    /**
     * @param array $nodes
     */
    public function fetchData(array $nodes)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        list($this->nodes, $this->pageIds, $this->pageUrls) = $this->_cmsPageModel->fetchData($nodes, $storeId);
    }

    /**
     * @param int $nodeId
     * @param int $level
     *
     * @return string
     */
    public function getHtml(int $nodeId, int $level)
    {
        $classes = $level == 0 ? 'level-top' : '';
        $node = $this->nodes[$nodeId];

        if (isset($this->pageIds[$node->getContent()])) {
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

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getAddButtonLabel()
    {
        return __("Add Cms Page link node");
    }
}