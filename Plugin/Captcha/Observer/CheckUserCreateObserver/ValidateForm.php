<?php
namespace MageSuite\SuccessPageRegistration\Plugin\Captcha\Observer\CheckUserCreateObserver;

class ValidateForm
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

    public function aroundExecute(\Magento\Captcha\Observer\CheckUserCreateObserver $subject, callable $proceed, \Magento\Framework\Event\Observer $observer)
    {
        if (strpos($this->redirect->getRefererUrl(), 'checkout/onepage/success') !== false) {
            return $subject;
        }

        return $proceed($observer);
    }
}
