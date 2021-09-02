<?php
/**
 * Snowdog
 *
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model\NodeType;

use Magento\Framework\Profiler;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\TemplateResolver;

class Wrapper extends AbstractNode
{
    /**
     * @var TemplateResolver
     */
    private $templateResolver;

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

        $data = [
            'snowMenuNodeCustomTemplates' => [
                'defaultTemplate' => 'wrapper',
                'options' => $this->templateResolver->getCustomTemplateOptions('wrapper'),
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
    public function processNodeClone(NodeInterface $node, NodeInterface $nodeClone): void
    {
        parent::processNodeClone($node, $nodeClone);
    }
}
