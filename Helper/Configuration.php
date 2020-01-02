<?php

namespace MageSuite\SuccessPageRegistration\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CONFIGURATION_KEY = 'customer/create_account/source_address';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
    }

    public function getAddressType()
    {
        $addressType = $this->scopeConfig->getValue(self::XML_PATH_CONFIGURATION_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $addressType;
    }
}
