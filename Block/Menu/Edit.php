<?php

namespace Snowdog\Menu\Block\Menu;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Snowdog_Menu';
        $this->_controller = 'menu';
        $this->_mode = 'edit';
        parent::_construct();
    }
}