<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Framework\DataObject;
use SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionLabelInterface;

class CodedAttributeOptionLabel extends DataObject implements CodedAttributeOptionLabelInterface
{
    public function getStoreCode()
    {
        return $this->getData(CodedAttributeOptionLabelInterface::STORE_CODE);
    }

    public function setStoreCode($storeCode)
    {
        return $this->setData(CodedAttributeOptionLabelInterface::STORE_CODE, $storeCode);
    }

    public function getLabel()
    {
        return $this->getData(CodedAttributeOptionLabelInterface::LABEL);
    }

    public function setLabel($label)
    {
        return $this->setData(CodedAttributeOptionLabelInterface::LABEL, $label);
    }
}
