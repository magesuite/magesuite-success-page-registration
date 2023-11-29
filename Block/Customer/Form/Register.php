<?php

namespace MageSuite\SuccessPageRegistration\Block\Customer\Form;

class Register extends \Magento\Customer\Block\Form\Register
{
    protected \Magento\Checkout\Model\Session $checkoutSession;
    protected \Magento\Customer\Model\CustomerFactory $customerModelFactory;
    protected \Magento\Customer\Model\Registration $registration;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\CustomerFactory $customerModelFactory,
        \Magento\Customer\Model\Registration $registration,
        array $data = [],
        \Magento\Newsletter\Model\Config $newsLetterConfig = null
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerModelFactory = $customerModelFactory;
        $this->registration = $registration;

        // Compatibility with Magento >= 2.4.5,
        // For improved maintainability I don't want to introduce breaking compatibility version just for that ViewModel
        //phpcs:disable
        if (class_exists('Magento\Customer\ViewModel\CreateAccountButton')) {
            $data['create_account_button_view_model'] = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magento\Customer\ViewModel\CreateAccountButton');
        }
        //phpcs:enable
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $moduleManager,
            $customerSession,
            $customerUrl,
            $data,
            $newsLetterConfig
        );
    }

    protected function _toHtml()
    {
        if (!$this->registration->isAllowed()) {
            return '';
        }

        if (!$this->_customerSession->isLoggedIn()) {
            $email = $this->checkoutSession->getLastRealOrder()->getCustomerEmail();
            if ($this->customerExists($email)) {
                return '';
            }
        }

        return parent::_toHtml();
    }

    protected function customerExists($email): bool
    {
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customer = $this->customerModelFactory->create();
        $customer->setWebsiteId($websiteId)->loadByEmail($email);

        return $customer->getId() > 0;
    }

    public function getSuccessUrl(): string
    {
        return $this->_customerUrl->getAccountUrl();
    }
}
