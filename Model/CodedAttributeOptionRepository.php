<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use SnowIO\AttributeOptionCode\Api\CodedAttributeOptionRepositoryInterface;
use SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface as MagentoAttributeOption;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\OptionManagement as ProductOptionManagement;
use Magento\Eav\Model\Config;
use Magento\Catalog\Model\Product;

class CodedAttributeOptionRepository implements CodedAttributeOptionRepositoryInterface
{
    private \Magento\Eav\Api\AttributeOptionManagementInterface $optionManagementService;
    private \SnowIO\AttributeOptionCode\Model\AttributeOptionCodeRepository $optionCodeRepository;
    private \SnowIO\AttributeOptionCode\Model\OptionConverter $optionConverter;
    private \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository;
    private ProductOptionManagement $productOptionManagement;
    private \Magento\Eav\Model\Config $eavConfig;

    /**
     * CodedAttributeOptionRepository constructor.
     * @param AttributeOptionManagementInterface $optionManagementService
     * @param AttributeOptionCodeRepository $optionCodeRepository
     * @param OptionConverter $optionConverter
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param ProductOptionManagement $productOptionManagement
     * @param Config $eavConfig
     */
    public function __construct(
        AttributeOptionManagementInterface $optionManagementService,
        \SnowIO\AttributeOptionCode\Api\AttributeOptionCodeRepositoryInterface $optionCodeRepository,
        OptionConverter $optionConverter,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ProductOptionManagement $productOptionManagement,
        Config $eavConfig
    ) {
        $this->optionManagementService = $optionManagementService;
        $this->optionCodeRepository = $optionCodeRepository;
        $this->optionConverter = $optionConverter;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productOptionManagement = $productOptionManagement;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param int $entityType
     * @param string $attributeCode
     * @param CodedAttributeOptionInterface $option
     * @throws \Magento\Framework\Exception\InputException
     */
    public function save($entityType, $attributeCode, CodedAttributeOptionInterface $option)
    {
        $optionCode = $option->getValue();
        $magentoOption = $this->optionConverter->convertCodedOptionToMagentoOption($entityType, $attributeCode, $option);

        if (null === $magentoOption->getValue()) {
            $this->addOption($entityType, $attributeCode, $optionCode, $magentoOption);
        } else {
            $this->updateOption($attributeCode, $magentoOption);
        }
    }

    /**
     * @param int $entityType
     * @param string $attributeCode
     * @param string $optionCode
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete($entityType, $attributeCode, $optionCode)
    {
        $optionId = $this->optionCodeRepository->getOptionId($entityType, $attributeCode, $optionCode);

        if (null === $optionId) {
            return;
        }

        try {
            $this->optionManagementService->delete($entityType, $attributeCode, $optionId);
        } catch (NoSuchEntityException $e) {
            // We don't care about deleting a non-existent item. Ignore this error
        }

        $this->optionCodeRepository->removeOption($entityType, $attributeCode, $optionCode, $optionId);
    }

    /**
     * @param $entityType
     * @param $attributeCode
     * @param $optionCode
     * @param MagentoAttributeOption $option
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function addOption($entityType, $attributeCode, $optionCode, MagentoAttributeOption $option)
    {
        if ($this->eavConfig->getEntityType($entityType)->getEntityTypeCode() === Product::ENTITY) {
            /**
             * If attribute option is for product entity type, call the product add method directly.
             * This must be called directly to ensure the swatch plugin is executed.
             * If the swatch plugin is not executed, swatch attribute options are not saved correctly.
             *
             * @see \Magento\Swatches\Plugin\Eav\Model\Entity\Attribute\OptionManagement::beforeAdd
             */
            $newOptionId = $this->productOptionManagement->add($attributeCode, $option);
        } else {
            $newOptionId = $this->optionManagementService->add($entityType, $attributeCode, $option);
        }

        // Magento returns the new option id prefixed with "id_"
        // We remove non numeric characters to get the correct option id
        $newOptionId = preg_replace("/[^0-9]/", "", $newOptionId);

        $this->optionCodeRepository->setOptionId($entityType, $attributeCode, $optionCode, $newOptionId);
    }

    /**
     * @param $attributeCode
     * @param MagentoAttributeOption $magentoOption
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function updateOption($attributeCode, MagentoAttributeOption $magentoOption)
    {
        $attribute = $this->productAttributeRepository->get($attributeCode);
        $options = $attribute->getOptions();
        foreach ($options as $option) {
            /** @var MagentoAttributeOption $option */
            if ((int)$option->getValue() === (int)$magentoOption->getValue()) {

                $option->setLabel($magentoOption->getLabel());
                $option->setSortOrder($magentoOption->getSortOrder());
                $option->setStoreLabels($magentoOption->getStoreLabels());
                $option->setIsDefault($magentoOption->getIsDefault());

                $attribute->setOptions([$option]);
                $this->productAttributeRepository->save($attribute);

                break;
            }
        }
    }
}
