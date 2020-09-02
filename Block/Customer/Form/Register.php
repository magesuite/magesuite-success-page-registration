<?php

namespace MageSuite\SuccessPageRegistration\Block\Customer\Form;

class Register extends \Magento\Customer\Block\Form\Register
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerModelFactory;

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
        array $data = [],
        \Magento\Newsletter\Model\Config $newsLetterConfig = null

    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerModelFactory = $customerModelFactory;

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
        if(!$this->_customerSession->isLoggedIn()) {
            $email = $this->checkoutSession->getLastRealOrder()->getCustomerEmail();
            if ($this->customerExists($email)) {
                return '';
            }
        }

        return parent::_toHtml();
    }

    protected function customerExists($email)
    {
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

        $customer = $this->customerModelFactory->create();
        $customer->setWebsiteId($websiteId)->loadByEmail($email);

        return $customer->getId() ? true : false;
    }
}
