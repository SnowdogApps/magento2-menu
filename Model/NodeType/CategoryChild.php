<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\NodeType;

use Magento\Framework\Profiler;
use Snowdog\Menu\Model\Menu\Node\Image\File as NodeImage;
use Snowdog\Menu\Model\NodeType\AbstractNode;
use Snowdog\Menu\Model\TemplateResolver;

class CategoryChild extends AbstractNode
{
    /**
     * @var TemplateResolver
     */
    private $templateResolver;

    /**
     * @param Profiler $profiler
     * @param NodeImage $nodeImage
     * @param TemplateResolver $templateResolver
     */
    public function __construct(
        Profiler $profiler,
        NodeImage $nodeImage,
        TemplateResolver $templateResolver
    ) {
        $this->templateResolver = $templateResolver;

        parent::__construct($profiler, $nodeImage);
    }

    /**
     * @inheritDoc
     */
    public function fetchConfigData()
    {
        $this->profiler->start(__METHOD__);

        $data = [
            'snowMenuNodeCustomTemplates' => [
                'defaultTemplate' => 'category_child',
                'options' => $this->templateResolver->getCustomTemplateOptions('category_child'),
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
        return [];
    }
}
