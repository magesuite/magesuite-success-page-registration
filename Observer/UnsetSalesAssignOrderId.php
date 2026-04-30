<?php

declare(strict_types=1);

namespace MageSuite\SuccessPageRegistration\Observer;

class UnsetSalesAssignOrderId implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        protected \Magento\Customer\Model\Session $customerSession
    ) {}

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $delegatedNewCustomerData = $this->customerSession->getDelegatedNewCustomerData();

        if (isset($delegatedNewCustomerData['delegated_data']['__sales_assign_order_id'])) {
            $this->customerSession->setCustomerFormData(null);
            $this->customerSession->setDelegatedNewCustomerData(null);
        }
    }
}
