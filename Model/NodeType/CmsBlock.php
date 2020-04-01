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

class CmsBlock extends AbstractNode
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
        $this->_init('Snowdog\Menu\Model\ResourceModel\NodeType\CmsBlock');
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
                'type'    => 'cms_block',
                'options' => $fieldOptions,
                'message' => __('CMS Block not found'),
            ],
            'snowMenuNodeCustomTemplates' => [
                'type'    => 'cms_block',
                'defaultTemplate' => 'cms_block',
                'options' => $this->templateResolver->getCustomTemplateOptions('cms_block'),
                'message' => __('Template not found'),
            ],
            'snowMenuSubmenuCustomTemplates' => [
                'type'    => 'cms_block',
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
        $blocksCodes = [];

        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $blocksCodes[] = $node->getContent();
        }

        $codes = $this->getResource()->fetchData($storeId, $blocksCodes);

        $content = [];

        foreach ($codes as $row) {
            $content[$row['identifier']] = $row['content'];
        }

        $this->profiler->stop(__METHOD__);

        return [$localNodes, $content];
    }
}
