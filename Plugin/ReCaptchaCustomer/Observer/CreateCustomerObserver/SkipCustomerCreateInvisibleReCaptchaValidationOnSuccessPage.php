<?php

namespace MageSuite\SuccessPageRegistration\Plugin\ReCaptchaCustomer\Observer\CreateCustomerObserver;

class SkipCustomerCreateInvisibleReCaptchaValidationOnSuccessPage
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    public function __construct(\Magento\Framework\App\Response\RedirectInterface $redirect)
    {
        $this->redirect = $redirect;
    }

    public function aroundExecute(\Magento\ReCaptchaCustomer\Observer\CreateCustomerObserver $subject, callable $proceed, \Magento\Framework\Event\Observer $observer)
    {
        if (strpos($this->redirect->getRefererUrl(), 'checkout/onepage/success') !== false) {
            return $subject;
        }

        return $proceed($observer);
    }
}
