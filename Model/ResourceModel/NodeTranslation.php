<?php
declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Snowdog\Menu\Api\Data\NodeTranslationInterface;

class NodeTranslation extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('snowmenu_node_translation', 'translation_id');
    }
}
