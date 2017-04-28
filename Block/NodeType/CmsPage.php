<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\View\Element\Template\Context;
use Magento\Cms\Api\Data\PageInterface;
use Snowdog\Menu\Model\NodeType\CmsPage as CmsPageModel;

class CmsPage extends AbstractNode
{
    /**
     * @var string
     */
    protected $nodeType = 'cms_page';
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
     * @var PageInterface
     */
    private $page;
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
     * @param Context $context
     * @param PageInterface $page
     * @param CmsPageModel $cmsPageModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        PageInterface $page,
        CmsPageModel $cmsPageModel,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->_cmsPageModel = $cmsPageModel;
        $this->page = $page;
    }

    /**
     * @return array
     */
    public function getNodeCacheKeyInfo()
    {
        $info = [];
        $pageId = $this->getRequest()->getParam('page_id');

        if ($pageId) {
            $info[] = 'cms_page_' . $pageId;
        }

        return $info;
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
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isCurrentPage(int $nodeId)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException('Invalid node identifier specified');
        }

        $node = $this->nodes[$nodeId];
        $nodeContent = $node->getContent();

        return isset($this->pageIds[$nodeContent])
            ? $this->page->getId() == $this->pageIds[$nodeContent]
            : false;
    }

    /**
     * @param int $nodeId
     * @param int|null $storeId
     * @return string|false
     * @throws \InvalidArgumentException
     */
    public function getPageUrl(int $nodeId, $storeId = null)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException('Invalid node identifier specified');
        }

        $node = $this->nodes[$nodeId];
        $nodeContent = $node->getContent();

        if (isset($this->pageIds[$nodeContent])) {
            $pageId = $this->pageIds[$nodeContent];
            $baseUrl = $this->_storeManager->getStore($storeId)->getBaseUrl();
            $pageUrlPath = $this->pageUrls[$pageId];

            return $baseUrl . $pageUrlPath;
        }

        return false;
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
