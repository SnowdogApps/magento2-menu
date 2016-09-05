<?php
namespace Snowdog\Menu\Ui\Component\Listing\DataProviders\Snowmenu\Menu;

class ListProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Snowdog\Menu\Model\ResourceModel\Menu\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
