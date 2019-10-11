<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use SnowIO\AttributeOptionCode\Api\CodedAttributeOptionRepositoryInterface;
use SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface as MagentoAttributeOption;

class CodedAttributeOptionRepository implements CodedAttributeOptionRepositoryInterface
{
    private $optionManagementService;
    private $optionCodeRepository;
    private $optionConverter;

    public function __construct(
        AttributeOptionManagementInterface $optionManagementService,
        AttributeOptionCodeRepository $optionCodeRepository,
        OptionConverter $optionConverter
    ) {
        $this->optionManagementService = $optionManagementService;
        $this->optionCodeRepository = $optionCodeRepository;
        $this->optionConverter = $optionConverter;
    }

    public function save($entityType, $attributeCode, CodedAttributeOptionInterface $option)
    {
        $optionCode = $option->getValue();
        $magentoOption = $this->optionConverter->convertCodedOptionToMagentoOption($entityType, $attributeCode, $option);

        if (null === $magentoOption->getValue()) {
            $this->addOption($entityType, $attributeCode, $optionCode, $magentoOption);
        } else {
            $this->optionManagementService->add($entityType, $attributeCode, $magentoOption);
        }
    }

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

    private function addOption($entityType, $attributeCode, $optionCode, MagentoAttributeOption $option)
    {
        $newOptionId = $this->optionManagementService->add($entityType, $attributeCode, $option);

        // Magento returns the new option id prefixed with "id_"
        // We remove non numeric characters to get the correct option id
        $newOptionId = preg_replace("/[^0-9]/", "", $newOptionId);

        $this->optionCodeRepository->setOptionId($entityType, $attributeCode, $optionCode, $newOptionId);
    }
}
