<?php

namespace Snowdog\Menu\Model;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;

class CustomerGroupsProvider
{
    private GroupCollectionFactory $groupCollectionFactory;

    public function __construct(
        GroupCollectionFactory $groupCollectionFactory,
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    public function getAll()
    {
        $customerGroups = [];
        $customerGroups[] = [
            'label' => __('All Customer Groups'),
            'value' => ""
        ];

        /** @var \Magento\Customer\Model\Group $customerGroup */
        foreach($this->groupCollectionFactory->create() as $customerGroup) {
            $customerGroups[] = [
                'label' => $customerGroup->getCode(),
                'value' => $customerGroup->getId()
            ];
        }

        return $customerGroups;
    }

}
