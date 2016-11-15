<?php
namespace SnowIO\AttributeOptionCode\Api;

interface CodedAttributeOptionRepositoryInterface
{
    /**
     * Save option
     *
     * @param int $entityType
     * @param string $attributeCode
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $option
     * @return \Magento\Eav\Api\Data\AttributeOptionInterface
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function save($entityType, $attributeCode, $option);

    /**
     * Delete option from attribute
     *
     * @param int $entityType
     * @param string $attributeCode
     * @param string $optionCode
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function delete($entityType, $attributeCode, $optionCode);
}
