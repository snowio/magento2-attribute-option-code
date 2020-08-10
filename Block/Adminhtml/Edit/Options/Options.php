<?php

namespace SnowIO\AttributeOptionCode\Block\Adminhtml\Edit\Options;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options as MagentoEavOptions;
use SnowIO\AttributeOptionCode\Api\AttributeOptionCodeRepositoryInterface;

class Options extends MagentoEavOptions
{
    /** @var AttributeOptionCodeRepositoryInterface */
    private $attributeOptionCodeRepository;

    /**
     * @var string
     */
    protected $_template = 'SnowIO_AttributeOptionCode::catalog/product/attribute/options.phtml';

    /**
     * Options constructor.
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $attrOptionCollectionFactory
     * @param UniversalFactory $universalFactory
     * @param AttributeOptionCodeRepositoryInterface $attributeOptionCodeRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $attrOptionCollectionFactory,
        UniversalFactory $universalFactory,
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
        if (!$values) {
            return null;
        }

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
