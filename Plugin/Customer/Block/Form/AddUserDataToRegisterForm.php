<?php

namespace MageSuite\SuccessPageRegistration\Plugin\Customer\Block\Form;

class AddUserDataToRegisterForm
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository
     */
    protected $orderAddressRepository;

    /**
     * @var \Magento\Framework\App\Request\Http $request
     */
    protected $request;

    /**
     * @var Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\Session\Proxy $sessionProxy
     */
    protected $sessionProxy;

    /**
     * @var \Magento\Sales\Model\Order\OrderCustomerExtractor $customerExtractor
     */
    protected $customerExtractor;

    /**
     * @var \MageSuite\SuccessPageRegistration\Helper\Configuration
     */
    protected $configurationHelper;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Session\Proxy $sessionProxy,
        \Magento\Sales\Model\Order\OrderCustomerExtractor $customerExtractor,
        \MageSuite\SuccessPageRegistration\Helper\Configuration $configurationHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->sessionProxy = $sessionProxy;
        $this->customerExtractor = $customerExtractor;
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

        $customer = $this->customerExtractor->extract($lastOrderData->getId());

        $customerData = $customer->__toArray();
        $addressesData = [];
        if ($customer->getAddresses()) {
            /** @var Address $address */
            foreach ($customer->getAddresses() as $address) {
                $addressesData[] = $address->__toArray();
            }
        }

        $this->sessionProxy->setDelegatedNewCustomerData([
            'customer' => $customerData,
            'addresses' => $addressesData,
            'delegated_data' => ['__sales_assign_order_id' => $lastOrderData->getId()],
        ]);

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
