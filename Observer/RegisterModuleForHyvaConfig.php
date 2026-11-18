<?php

declare(strict_types=1);

namespace Snowdog\Menu\Observer;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Filesystem\DirectoryList;

class RegisterModuleForHyvaConfig implements ObserverInterface
{
    private $componentRegistrar;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    public function __construct(ComponentRegistrar $componentRegistrar, DirectoryList $directoryList)
    {
        $this->componentRegistrar = $componentRegistrar;
        $this->directoryList = $directoryList;
    }

    public function execute(Observer $event)
    {
        $config = $event->getData('config');
        $extensions = $config->hasData('extensions') ? $config->getData('extensions') : [];

        $path = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Snowdog_Menu');

        // Only use the path relative to the Magento base dir
        $extensions[] = ['src' => substr($path, strlen($this->directoryList->getRoot()) + 1)];

        $config->setData('extensions', $extensions);
    }
}
