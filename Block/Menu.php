<?php

namespace Snowdog\Menu\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

class Menu extends Template implements IdentityInterface
{

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        // TODO: Implement getIdentities() method.
    }
}