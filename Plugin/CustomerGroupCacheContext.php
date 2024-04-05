<?php
declare(strict_types=1);

namespace Snowdog\Menu\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context as HttpContext;

class CustomerGroupCacheContext
{
    /** @var Session  */
    private $customerSession;

    /** @var ScopeConfigInterface  */
    private $scopeConfig;

    public function __construct(
        Session $customerSession,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
    }

    public function beforeGetVaryString(HttpContext $subject)
    {
        if (!$this->scopeConfig->getValue('snowmenu/general/customer_groups')) {
            return [];
        }

        $currentCustomerGroup = $this->customerSession->getCustomerGroupId();
        $subject->setValue('customer_group', $currentCustomerGroup, 0);

        return [];
    }
}
