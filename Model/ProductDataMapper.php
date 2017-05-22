<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use SnowIO\AttributeOptionCode\Api\CodedAttributeOptionRepositoryInterface as CodedOptionRepository;
use SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionInterface as CodedOption;
use SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionInterfaceFactory as CodedOptionFactory;

class ProductDataMapper
{
    private $codedOptionRepository;
    private $codedOptionFactory;
    private $productAttributeManagement;
    private $codesOfAttributesToMap = [];
    private $attributeOptionCodeRepository;

    const PRODUCT_ENTITY_TYPE_ID = 4;

    public function __construct(
        CodedOptionRepository $codedOptionRepository,
        CodedOptionFactory $codedOptionFactory,
        ProductAttributeManagementInterface $productAttributeManagement,
        AttributeOptionCodeRepository $attributeOptionCodeRepository
    ) {
        $this->codedOptionRepository = $codedOptionRepository;
        $this->codedOptionFactory = $codedOptionFactory;
        $this->productAttributeManagement = $productAttributeManagement;
        $this->attributeOptionCodeRepository = $attributeOptionCodeRepository;
    }

    public function replaceOptionCodesWithOptionIds(ProductInterface $product)
    {
        $codesOfAttributesToMap = $this->getCodesOfAttributesToMap($product->getAttributeSetId());

        foreach ($codesOfAttributesToMap as $attributeCode) {
            if (null !== $customAttribute = $product->getCustomAttribute($attributeCode)) {
                $this->replaceOptionCodeWithOptionId($customAttribute, $product);
            }
        }
    }

    public function replaceOptionIdsWithOptionCodes(ProductInterface $product)
    {
        $codesOfAttributesToMap = $this->getCodesOfAttributesToMap($product->getAttributeSetId());

        foreach ($codesOfAttributesToMap as $attributeCode) {
            if (null !== $customAttribute = $product->getCustomAttribute($attributeCode)) {
                $this->replaceOptionIdWithOptionCode($customAttribute, $product);
            }
        }
    }

    private function getCodesOfAttributesToMap($attributeSetId)
    {
        if (!isset($this->codesOfAttributesToMap[$attributeSetId])) {
            $attributes = $this->productAttributeManagement->getAttributes($attributeSetId);
            $attributeCodes = [];
            foreach ($attributes as $attribute) {
                if ($this->shouldMapAttribute($attribute)) {
                    $attributeCodes[] = $attribute->getAttributeCode();
                }
            }
            $this->codesOfAttributesToMap[$attributeSetId] = $attributeCodes;
        }

        return $this->codesOfAttributesToMap[$attributeSetId];
    }

    private function replaceOptionCodeWithOptionId(AttributeInterface $customAttribute, ProductInterface $product)
    {
        $attributeCode = $customAttribute->getAttributeCode();
        $optionCodeOrCodes = $customAttribute->getValue();

        if (is_array($optionCodeOrCodes)) {
            // suppress errors below because array_map generates a warning when the map fn throws
            $optionIdOrIds = @array_map(function ($optionCode) use ($attributeCode) {
                return $this->getOrCreateOptionId($attributeCode, $optionCode);
            }, $optionCodeOrCodes);
        } else {
            $optionIdOrIds = $this->getOrCreateOptionId($attributeCode, $optionCodeOrCodes);
        }

        $product->setCustomAttribute($attributeCode, $optionIdOrIds);
    }

    private function getOrCreateOptionId($attributeCode, $optionCode)
    {
        $optionCode = (string)$optionCode;

        if ('' === $optionCode) {
            return null;
        }

        $optionId = $this->attributeOptionCodeRepository
            ->getOptionId(self::PRODUCT_ENTITY_TYPE_ID, $attributeCode, $optionCode);

        if (null === $optionId) {
            /** @var CodedOption $codedOption */
            $codedOption = $this->codedOptionFactory->create()
                ->setValue($optionCode)
                ->setLabel($optionCode);
            $this->codedOptionRepository->save(self::PRODUCT_ENTITY_TYPE_ID, $attributeCode, $codedOption);
            $optionId = $this->attributeOptionCodeRepository
                ->getOptionId(self::PRODUCT_ENTITY_TYPE_ID, $attributeCode, $optionCode);
        }

        return $optionId;
    }

    private function replaceOptionIdWithOptionCode(AttributeInterface $customAttribute, ProductInterface $product)
    {
        $optionId = (string)$customAttribute->getValue();
        if ('' !== $optionId) {
            $optionCode = $this->attributeOptionCodeRepository
                ->getOptionCode(self::PRODUCT_ENTITY_TYPE_ID, $customAttribute->getAttributeCode(), $optionId);
            if (null === $optionCode) {
                throw new \RuntimeException("Option ID {$optionId} for attribute ".
                    "{$customAttribute->getAttributeCode()} does not have an associated option code.");
            }
            $product->setCustomAttribute($customAttribute->getAttributeCode(), $optionCode);
        }
    }

    private function shouldMapAttribute(\Magento\Eav\Api\Data\AttributeInterface $attribute)
    {
        $sourceModel = $attribute->getSourceModel();

        if (null === $sourceModel) {
            return in_array($attribute->getFrontendInput(), ['select', 'multiselect']);
        }

        return $sourceModel === Table::class;
    }
}
