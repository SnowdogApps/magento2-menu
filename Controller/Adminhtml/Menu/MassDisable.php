<?php

namespace Snowdog\Menu\Controller\Adminhtml\Menu;

use Snowdog\Menu\Model\ResourceModel\Menu\Collection;

class MassDisable extends MassActionAbstract
{
    protected function process(Collection $collection)
    {
        $this->menuRepository->setIsActiveByIds($collection->getAllIds(), 0);
    }
}
