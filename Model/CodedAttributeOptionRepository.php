<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use SnowIO\AttributeOptionCode\Api\CodedAttributeOptionRepositoryInterface;

class CodedAttributeOptionRepository implements CodedAttributeOptionRepositoryInterface
{
    private $optionManagementService;
    private $optionCodeRepository;

    public function __construct(
        AttributeOptionManagementInterface $optionManagementService,
        AttributeOptionCodeRepository $optionCodeRepository
    ) {
        $this->optionManagementService = $optionManagementService;
        $this->optionCodeRepository = $optionCodeRepository;
    }

    public function save($entityType, $attributeCode, $option)
    {
        $optionCode = $option->getValue();

        if (null === $optionCode) {
            throw new InputException;
        }

        $existingOptionId = $this->optionCodeRepository->getOptionId($entityType, $attributeCode, $optionCode);
        $option->setValue($existingOptionId);

        if (null === $existingOptionId) {
            $this->addOption($entityType, $attributeCode, $optionCode, $option);
        } else {
            $this->optionManagementService->add($entityType, $attributeCode, $option);
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

    private function addOption($entityType, $attributeCode, $optionCode, $option)
    {
        $maxExistingOptionId = $this->optionCodeRepository->getMaxOptionId($entityType, $attributeCode) ?: 0;
        $this->optionManagementService->add($entityType, $attributeCode, $option);
        $newOptionId = $this->optionCodeRepository->getMaxOptionId($entityType, $attributeCode) ?: 0;

        if ($newOptionId <= $maxExistingOptionId) {
            throw new \RuntimeException('Failed to add option.');
        }

        $this->optionCodeRepository->setOptionId($entityType, $attributeCode, $optionCode, $newOptionId);
    }
}
