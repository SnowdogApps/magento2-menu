<?php

declare(strict_types=1);

namespace Snowdog\Menu\Block\Adminhtml\Import\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Back implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back'
        ];
    }

    private function getBackUrl(): string
    {
        return $this->context->getUrlBuilder()->getUrl('*/*');
    }
}
