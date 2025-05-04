<?php
declare(strict_types=1);

namespace Snowdog\Menu\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Snowdog\Menu\Api\Data\NodeTranslationInterface;

class NodeTranslation extends AbstractDb
{
    /**
     * @var string
     */
    public const TABLE_NAME = 'snowmenu_node_translation';

    /**
     * @var string
     */
    public const ID_FIELD_NAME = 'translation_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
