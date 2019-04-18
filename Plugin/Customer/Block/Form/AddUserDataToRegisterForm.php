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
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->request = $request;
        $this->customerSession = $customerSession;
    }

    public function afterGetFormData(\Magento\Customer\Block\Form\Register $subject, $result)
    {
        $action = $this->request->getActionName();

        $lastOrderData = $this->checkoutSession->getLastRealOrder();

        if (!$lastOrderData || $action !== 'success') {
            return $result;
        }

        $email = $lastOrderData->getCustomerEmail();

        $address = $this->orderAddressRepository->get($lastOrderData->getShippingAddressId());

        $subject->getData('form_data')->setEmail($email);
        $subject->getData('form_data')->setFirstname($address->getFirstname());
        $subject->getData('form_data')->setLastname($address->getLastname());

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