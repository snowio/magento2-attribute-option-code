<?php

namespace SnowIO\AttributeOptionCode\Block\Adminhtml\Edit\Options;

use Magento\Catalog\Setup\CategorySetup;
use SnowIO\AttributeOptionCode\Api\AttributeOptionCodeRepositoryInterface;

class Options extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options
{
    /** @var AttributeOptionCodeRepositoryInterface */
    private $attributeOptionCodeRepository;

    /**
     * @var string
     */
    protected $_template = 'SnowIO_AttributeOptionCode::catalog/product/attribute/options.phtml';

    /**
     * Options constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param AttributeOptionCodeRepositoryInterface $attributeOptionCodeRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        AttributeOptionCodeRepositoryInterface $attributeOptionCodeRepository,
        array $data = []
    ) {
        $this->attributeOptionCodeRepository = $attributeOptionCodeRepository;
        parent::__construct($context, $registry, $attrOptionCollectionFactory, $universalFactory, $data);
    }

    /**
     * @return array|null
     */
    public function getOptionValues()
    {
        $values = parent::getOptionValues();
        return $this->addOptionCodeToOptionValuesData($values);
    }

    /**
     * @param $values
     * @return array
     */
    private function addOptionCodeToOptionValuesData(array $values): array
    {
        $ids = $this->getIdsFromValuesData($values);
        $attributeCode = $this->getAttributeObject()->getAttributeCode();
        $optionCodes = $this->getOptionCodes($attributeCode, $ids);

        return $this->mapOptionCodeWithValuesData($optionCodes, $values);
    }

    /**
     * @param string $attributeCode
     * @param array $ids
     * @return array
     */
    private function getOptionCodes(string $attributeCode, array $ids): array
    {
        return $this
            ->attributeOptionCodeRepository
            ->getOptionCodes(CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID, $attributeCode, $ids);
    }

    /**
     * @param array $result
     * @return array
     */
    private function getIdsFromValuesData(array $values): array
    {
        return array_map(function ($item) {
            return $item->getData('id');
        }, $values);
    }

    /**
     * @param array $optionCode
     * @param array $values
     * @return array
     */
    private function mapOptionCodeWithValuesData(array $optionCode, array $values): array
    {
        return array_map(function ($item) use ($optionCode) {
            array_key_exists($item->getData('id'), $optionCode) ?
                $item->setData('option_code', $optionCode[$item['id']]) :
                $item->setData('option_code', " ");
            return $item;
        }, $values);
    }
}
