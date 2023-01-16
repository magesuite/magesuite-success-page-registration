<?php
declare(strict_types=1);

namespace MageSuite\SuccessPageRegistration\Plugin\Customer\Block\Form;

class AddUserDataToRegisterForm
{
    protected \Magento\Checkout\Model\Session $checkoutSession;

    protected \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository;

    protected \Magento\Framework\App\Request\Http $request;

    protected \Magento\Customer\Model\Session $customerSession;

    protected \Magento\Sales\Model\Order\OrderCustomerExtractor $customerExtractor;

    protected \Magento\Customer\Model\Delegation\Storage $storage;

    protected \MageSuite\SuccessPageRegistration\Helper\Configuration $configurationHelper;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\OrderCustomerExtractor $customerExtractor,
        \Magento\Customer\Model\Delegation\Storage $storage,
        \MageSuite\SuccessPageRegistration\Helper\Configuration $configurationHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->customerExtractor = $customerExtractor;
        $this->storage = $storage;
        $this->configurationHelper = $configurationHelper;
    }

    public function afterGetFormData(\Magento\Customer\Block\Form\Register $subject, $result)
    {
        $action = $this->request->getActionName();
        $lastOrderData = $this->checkoutSession->getLastRealOrder();

        if (!$lastOrderData || $action !== 'success') {
            return $result;
        }

        $email = $lastOrderData->getCustomerEmail();
        $addressType = $this->configurationHelper->getAddressType();

        if ($addressType == \Magento\Customer\Model\Address\AbstractAddress::TYPE_BILLING) {
            $address = $this->orderAddressRepository->get($lastOrderData->getBillingAddressId());
        } else {
            $address = $this->orderAddressRepository->get($lastOrderData->getShippingAddressId());
        }

        $subject->getData('form_data')->setEmail($email);
        $subject->getData('form_data')->setFirstname($address->getFirstname());
        $subject->getData('form_data')->setLastname($address->getLastname());
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
