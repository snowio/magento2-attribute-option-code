<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;
use Magento\Store\Api\StoreRepositoryInterface;
use SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionInterface as CodedOption;
use Magento\Eav\Api\Data\AttributeOptionInterface as MagentoOption;
use SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionLabelInterface as CodedOptionLabel;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface as MagentoOptionLabel;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory as MagentoOptionFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory as MagentoOptionLabelFactory;

class OptionConverter
{
    private \SnowIO\AttributeOptionCode\Model\AttributeOptionCodeRepository $optionCodeRepository;
    private \Magento\Store\Api\StoreRepositoryInterface $storeRepository;
    private MagentoOptionFactory $magentoOptionFactory;
    private MagentoOptionLabelFactory $magentoOptionLabelFactory;
    private array $storeIds = [];

    public function __construct(
        \SnowIO\AttributeOptionCode\Api\AttributeOptionCodeRepositoryInterface $optionCodeRepository,
        StoreRepositoryInterface $storeRepository,
        MagentoOptionFactory $magentoOptionFactory,
        MagentoOptionLabelFactory $magentoOptionLabelFactory
    ) {
        $this->optionCodeRepository = $optionCodeRepository;
        $this->storeRepository = $storeRepository;
        $this->magentoOptionFactory = $magentoOptionFactory;
        $this->magentoOptionLabelFactory = $magentoOptionLabelFactory;
    }

    /**
     * @param int $entityType
     * @param string $attributeCode
     * @return MagentoOption
     */
    public function convertCodedOptionToMagentoOption($entityType, $attributeCode, CodedOption $option)
    {
        $optionCode = $option->getValue();

        if (null === $optionCode) {
            throw new InputException(new Phrase('Missing required option code.'));
        }

        /** @var MagentoOption $magentoOption */
        $magentoOption = $this->magentoOptionFactory->create();
        $magentoOption->setLabel($option->getLabel());
        $existingOptionId = $this->optionCodeRepository->getOptionId($entityType, $attributeCode, $optionCode);
        $magentoOption->setValue($existingOptionId);
        $magentoOption->setSortOrder($option->getSortOrder());
        $magentoOption->setIsDefault($option->getIsDefault());

        if (null !== $labels = $option->getStoreLabels()) {
            $magentoOption->setStoreLabels(array_map(fn(CodedOptionLabel $optionLabel) => $this->convertCodedOptionLabelToMagentoOptionLabel($optionLabel), $labels));
        }

        return $magentoOption;
    }

    /**
     * @return MagentoOptionLabel
     */
    public function convertCodedOptionLabelToMagentoOptionLabel(CodedOptionLabel $optionLabel)
    {
        /** @var MagentoOptionLabel $magentoOptionLabel */
        $magentoOptionLabel = $this->magentoOptionLabelFactory->create();
        $magentoOptionLabel->setLabel($optionLabel->getLabel());

        if ($storeCode = $optionLabel->getStoreCode()) {
            $magentoOptionLabel->setStoreId($this->getStoreId($storeCode));
        }

        return $magentoOptionLabel;
    }

    private function getStoreId($storeCode)
    {
        if (!isset($this->storeIds[$storeCode])) {
            $store = $this->storeRepository->get($storeCode);
            $storeId = $store->getId();
            $this->storeIds[$storeCode] = $storeId;
        }

        return $this->storeIds[$storeCode];
    }
}
