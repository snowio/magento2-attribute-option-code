<?php
namespace SnowIO\AttributeOptionCode\Api;

interface AttributeOptionCodeRepositoryInterface
{
    /**
     * @param int $entityType
     * @param string $attributeCode
     * @param int $optionId
     * @return string|null
     */
    public function getOptionCode($entityType, $attributeCode, $optionId);

    /**
     * @param int $entityType
     * @param string $attributeCode
     * @param int[] $optionIds
     * @return array
     */
    public function getOptionCodes($entityType, $attributeCode, $optionIds);
}
