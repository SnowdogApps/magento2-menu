<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\View\Element\Template\Context;
use Magento\Cms\Model\Template\FilterProvider;
use Snowdog\Menu\Model\TemplateResolver;
use Snowdog\Menu\Model\NodeType\CmsBlock as CmsBlockModel;

class CmsBlock extends AbstractNode
{
    /**
     * @var string
     */
    protected $defaultTemplate = 'menu/node_type/cms_block.phtml';

    /**
     * @var string
     */
    protected $nodeType = 'cms_block';
    /**
     * @var array
     */
    protected $nodes;
    /**
     * @var array
     */
    protected $content;
    /**
     * {@inheritdoc}
     */
    protected $viewAllLink = false;

    /**
     * @var FilterProvider
     */
    private $filterProvider;
    /**
     * @var CmsBlockModel
     */
    private $_cmsBlockModel;

    /**
     * CmsBlock constructor.
     *
     * @param Context $context
     * @param CmsBlockModel $cmsBlockModel
     * @param FilterProvider $filterProvider
     * @param TemplateResolver $templateResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        CmsBlockModel $cmsBlockModel,
        FilterProvider $filterProvider,
        TemplateResolver $templateResolver,
        $data = []
    ) {
        parent::__construct($context, $templateResolver, $data);
        $this->filterProvider = $filterProvider;
        $this->_cmsBlockModel = $cmsBlockModel;
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $data = $this->_cmsBlockModel->fetchConfigData();

        return $data;
    }

    /**
     * @param array $nodes
     */
    public function fetchData(array $nodes)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        list($this->nodes, $this->content) = $this->_cmsBlockModel->fetchData($nodes, $storeId);
    }

    /**
     * @param int $nodeId
     * @param int $level
     *
     * @return mixed|string
     */
    public function getHtml($nodeId, $level)
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

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __("Cms Block");
    }
}
