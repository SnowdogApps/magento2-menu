<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\View\Element\Template\Context;
use Magento\Cms\Api\Data\PageInterface;
use Snowdog\Menu\Model\TemplateResolver;
use Snowdog\Menu\Model\NodeType\CmsPage as CmsPageModel;

class CmsPage extends AbstractNode
{
    /**
     * @var string
     */
    protected $defaultTemplate = 'menu/node_type/cms_page.phtml';

    /**
     * @var string
     */
    protected $customTemplateFolder = 'menu/custom/cms_page/';

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
     * @var CmsPageModel
     */
    private $_cmsPageModel;

    /**
     * CmsPage constructor.
     *
     * @param Context $context
     * @param PageInterface $page
     * @param CmsPageModel $cmsPageModel
     * @param TemplateResolver $templateResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        PageInterface $page,
        CmsPageModel $cmsPageModel,
        TemplateResolver $templateResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $data = []
    ) {
        parent::__construct($context, $templateResolver, $data);
        $this->_cmsPageModel = $cmsPageModel;
        $this->page = $page;
        $this->storesList = $storeManager->getStores();
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
     * @inheritDoc
     */
    public function getJsonConfig()
    {
        $options = $this->_cmsPageModel->fetchConfigData();

        $options = array_map(function ($page) {
            $page['store'] = array_map(function ($id) {
                return $this->storesList[$id]['name'];
            }, $page['store']);

            return $page;
        }, $options);

        return [
            'snowMenuAutoCompleteField' => [
                'type' => 'cms_page',
                'options' => array_values($options),
                'message' => __('CMS Page not found'),
            ],
            'snowMenuNodeCustomTemplates' => [
                'defaultTemplate' => 'cms_page',
                'options' => $this->templateResolver->getCustomTemplateOptions('cms_page'),
                'message' => __('Template not found'),
            ],
            'snowMenuSubmenuCustomTemplates' => [
                'defaultTemplate' => 'sub_menu',
                'options' => $this->templateResolver->getCustomTemplateOptions('sub_menu'),
                'message' => __('Template not found'),
            ]
        ];
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
    public function isCurrentPage($nodeId)
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
    public function getPageUrl($nodeId, $storeId = null)
    {
        if (!isset($this->nodes[$nodeId])) {
            throw new \InvalidArgumentException('Invalid node identifier specified');
        }

        $node = $this->nodes[$nodeId];
        $nodeContent = $node->getContent();

        if (isset($this->pageIds[$nodeContent])) {
            $pageId = $this->pageIds[$nodeContent];
            $baseUrl = $this->_storeManager->getStore($storeId)->getBaseUrl();
            $pageUrlPath = (isset($this->pageUrls[$pageId]))
                ? $this->pageUrls[$pageId]
                :'';
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
    public function getHtml($nodeId, $level)
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
    public function getLabel()
    {
        return __("Cms Page link");
    }
}
