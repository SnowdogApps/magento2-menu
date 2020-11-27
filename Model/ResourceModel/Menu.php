<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Menu extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('snowmenu_menu', 'menu_id');
    }

    public function getFields(): array
    {
        return $this->getConnection()->describeTable($this->getMainTable());
    }
}
