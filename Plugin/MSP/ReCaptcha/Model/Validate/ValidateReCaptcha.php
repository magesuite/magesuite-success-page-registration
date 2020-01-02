<?php

namespace MageSuite\SuccessPageRegistration\Plugin\MSP\ReCaptcha\Model\Validate;

class ValidateReCaptcha
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    public function __construct(
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->redirect = $redirect;
    }

    public function aroundValidate(\MSP\ReCaptcha\Model\Validate $subject, callable $proceed, $reCaptchaResponse, $remoteIp)
    {
        if (strpos($this->redirect->getRefererUrl(), 'checkout/onepage/success') !== false) {
            return true;
        }

        return $proceed($reCaptchaResponse, $remoteIp);
    }
}
