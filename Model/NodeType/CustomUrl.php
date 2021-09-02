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
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\TemplateResolver;

class CustomUrl extends AbstractNode
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
                'defaultTemplate' => 'custom_url',
                'options' => $this->templateResolver->getCustomTemplateOptions('custom_url'),
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
