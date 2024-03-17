<?php

namespace Snowdog\Menu\Model;

use Magento\Customer\Model\Group;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;

class CustomerGroupsProvider
{
    /**
     * @var GroupCollectionFactory
     */
    private $groupCollectionFactory;

    public function __construct(
        GroupCollectionFactory $groupCollectionFactory
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    public function getAll()
    {
        $customerGroups = [];

        /** @var Group $customerGroup */
        foreach ($this->groupCollectionFactory->create() as $customerGroup) {
            $customerGroups[] = [
                'label' => $customerGroup->getCode(),
                'value' => $customerGroup->getId()
            ];
        }

        return $customerGroups;
    }
}
