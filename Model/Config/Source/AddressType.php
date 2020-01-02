<?php

namespace MageSuite\SuccessPageRegistration\Model\Config\Source;

class AddressType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (empty($this->options)) {
            $this->options = [
                ['label' => 'Billing Address', 'value' => \Magento\Customer\Model\Address\AbstractAddress::TYPE_BILLING],
                ['label' => 'Shipping Address', 'value' => \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING]
            ];
        }

        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
