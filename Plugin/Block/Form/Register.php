<?php

namespace MageSuite\SuccessPageRegistration\Plugin\Block\Form;

class Register
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
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(\Magento\Checkout\Model\Session $checkoutSession, \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository)
    {
        $this->checkoutSession = $checkoutSession;
        $this->orderAddressRepository = $orderAddressRepository;
    }

    public function afterGetFormData(\Magento\Customer\Block\Form\Register $subject, $result)
    {
        $lastOrderData = $this->checkoutSession->getLastRealOrder();

        $email = $lastOrderData->getCustomerEmail();

        $address = $this->orderAddressRepository->get($lastOrderData->getShippingAddressId());

        $subject->getData('form_data')->setEmail($email);
        $subject->getData('form_data')->setFirstname($address->getFirstname());
        $subject->getData('form_data')->setLastname($address->getLastname());

        return $result;
    }

}