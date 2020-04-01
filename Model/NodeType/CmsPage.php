<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model\NodeType;

use Magento\Framework\Profiler;
use Snowdog\Menu\Model\TemplateResolver;

class CmsPage extends AbstractNode
{
    /**
     * @var TemplateResolver
     */
    private $templateResolver;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Snowdog\Menu\Model\ResourceModel\NodeType\CmsPage');
        parent::_construct();
    }

    public function __construct(
        Profiler $profiler,
        TemplateResolver $templateResolver
    ) {
        $this->templateResolver = $templateResolver;
        parent::__construct($profiler);
    }

    /**
     * @inheritDoc
     */
    public function fetchConfigData()
    {
        $this->profiler->start(__METHOD__);

        $options = $this->getResource()->fetchConfigData();
        $fieldOptions = [];

        foreach ($options as $label => $value) {
            $fieldOptions[] = [
                'label' => $label,
                'value' => $value
            ];
        }

        $data = [
            'snowMenuAutoCompleteField' => [
                'type' => 'cms_page',
                'options' => $fieldOptions,
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
            ],
        ];

        $this->profiler->stop(__METHOD__);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function fetchData(array $nodes, $storeId)
    {
        $this->profiler->start(__METHOD__);

        $localNodes = [];
        $pagesCodes = [];

        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $pagesCodes[] = $node->getContent();
        }

        /** @var \Snowdog\Menu\Model\ResourceModel\NodeType\CmsPage $resource */
        $resource = $this->getResource();
        $pageIds = $resource->getPageIds($storeId, $pagesCodes);
        $pageUrls = $resource->fetchData($storeId, $pageIds);


        $this->profiler->stop(__METHOD__);

        return [$localNodes, $pageIds, $pageUrls];
    }
}
