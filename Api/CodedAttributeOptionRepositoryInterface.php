<?php
namespace SnowIO\AttributeOptionCode\Api;

interface CodedAttributeOptionRepositoryInterface
{
    /**
     * Save option
     *
     * @param string $attributeCode
     * @param int $entityType
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $option
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
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function delete($entityType, $attributeCode, $optionCode);
}
