<?php
namespace Snowdog\Menu\Model\ResourceModel\Menu\Node;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Sql\Expression;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Block\Menu;

class Collection extends AbstractCollection
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ScopeConfigInterface $scopeConfig,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init(
            \Snowdog\Menu\Model\Menu\Node::class,
            \Snowdog\Menu\Model\ResourceModel\Menu\Node::class
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
    
        if (!$this->scopeConfig->isSetFlag(Menu::XML_SNOWMENU_GENERAL_CUSTOMER_GROUPS)) {
            return $this;
        }
    
        $customerTable = $this->getTable('snowmenu_customer');
    
        $this->getSelect()->joinLeft(
            ['customer' => $customerTable],
            'main_table.node_id = customer.node_id',
            ['customer_groups' => new Expression('GROUP_CONCAT(group_id SEPARATOR \',\')')]
        )->group('main_table.node_id');
    
        return $this;
    }
}
