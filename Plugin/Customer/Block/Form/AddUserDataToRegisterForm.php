<?php
declare(strict_types=1);

namespace MageSuite\SuccessPageRegistration\Plugin\Customer\Block\Form;

class AddUserDataToRegisterForm
{
    public function __construct(
        protected \Magento\Checkout\Model\Session $checkoutSession,
        protected \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository,
        protected \Magento\Framework\App\Request\Http $request,
        protected \Magento\Customer\Model\Session $customerSession,
        protected \Magento\Sales\Model\Order\OrderCustomerExtractor $customerExtractor,
        protected \Magento\Customer\Model\Delegation\Storage $storage,
        protected \MageSuite\SuccessPageRegistration\Helper\Configuration $configurationHelper
    ) {}

    public function afterGetFormData(\Magento\Customer\Block\Form\Register $subject, $result)
    {
        $action = $this->request->getActionName();
        $lastOrderData = $this->checkoutSession->getLastRealOrder();

        if (!$lastOrderData || $action !== 'success') {
            return $result;
        }

        $email = $lastOrderData->getCustomerEmail();
        $addressId = $lastOrderData->getBillingAddressId();
        $addressType = $this->configurationHelper->getAddressType();

        if ($addressType == \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING && !$lastOrderData->getIsVirtual()) {
            $addressId = $lastOrderData->getShippingAddressId();
        }

        $address = $this->orderAddressRepository->get($addressId);
        $subject->getData('form_data')->setEmail($email);
        $subject->getData('form_data')->setFirstname($address->getFirstname());
        $subject->getData('form_data')->setLastname($address->getLastname());
        $subject->getData('form_data')->setDob($lastOrderData->getCustomerDob());
        $customer = $this->customerExtractor->extract((int)$lastOrderData->getId());
        $this->storage->storeNewOperation($customer, ['__sales_assign_order_id' => $lastOrderData->getId()]);

        return $result;
    }

    public function afterToHtml(\Magento\Customer\Block\Form\Register $subject, $result)
    {
        if ($this->customerSession->isLoggedIn()) {
            return '';
        }

        return $result;
    }
}
