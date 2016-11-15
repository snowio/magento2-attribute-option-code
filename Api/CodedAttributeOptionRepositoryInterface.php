<?php
namespace SnowIO\AttributeOptionCode\Api;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use SnowIO\AttributeOptionCode\Api\Data\AttributeOptionInterface;

interface CodedAttributeOptionRepositoryInterface
{
    /**
     * Save option
     *
     * @param int $entityType
     * @param string $attributeCode
     * @throws StateException
     * @throws InputException
     */
    public function save($entityType, $attributeCode, AttributeOptionInterface $option);

    /**
     * Delete option from attribute
     *
     * @param int $entityType
     * @param string $attributeCode
     * @param string $optionCode
     * @throws StateException
     * @throws InputException
     */
    public function delete($entityType, $attributeCode, $optionCode);
}
