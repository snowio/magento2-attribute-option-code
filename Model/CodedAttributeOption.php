<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Framework\DataObject;
use SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionInterface;

class CodedAttributeOption extends DataObject implements CodedAttributeOptionInterface
{
    public function getLabel()
    {
        return $this->getData(CodedAttributeOptionInterface::LABEL);
    }

    public function getValue()
    {
        return $this->getData(CodedAttributeOptionInterface::VALUE);
    }

    public function getSortOrder()
    {
        return $this->getData(CodedAttributeOptionInterface::SORT_ORDER);
    }

    public function getIsDefault()
    {
        return $this->getData(CodedAttributeOptionInterface::IS_DEFAULT);
    }

    public function getStoreLabels()
    {
        return $this->getData(CodedAttributeOptionInterface::STORE_LABELS);
    }

    public function setLabel($label)
    {
        return $this->setData(CodedAttributeOptionInterface::LABEL, $label);
    }

    public function setValue($value)
    {
        return $this->setData(CodedAttributeOptionInterface::VALUE, $value);
    }

    public function setSortOrder($sortOrder)
    {
        return $this->setData(CodedAttributeOptionInterface::SORT_ORDER, $sortOrder);
    }

    public function setIsDefault($isDefault)
    {
        return $this->setData(CodedAttributeOptionInterface::IS_DEFAULT, $isDefault);
    }

    public function setStoreLabels(array $storeLabels = null)
    {
        return $this->setData(CodedAttributeOptionInterface::STORE_LABELS, $storeLabels);
    }
}
