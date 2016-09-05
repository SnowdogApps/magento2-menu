<?php
namespace Snowdog\Menu\Ui\Component\Listing\DataProviders\Snowmenu\Menu;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Snowdog\Menu\Model\ResourceModel\Menu\CollectionFactory;

class ListProvider extends AbstractDataProvider
{    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
