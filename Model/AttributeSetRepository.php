<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Framework\App\CacheInterface;

class AttributeSetRepository
{
    private $productAttributeManagement;
    private $cache;

    public function __construct(
        ProductAttributeManagementInterface $productAttributeManagement,
        CacheInterface $cache
    ) {
        $this->productAttributeManagement = $productAttributeManagement;
        $this->cache = $cache;
    }

    public function getAttributesSupportingOptionCodes($attributeSetId)
    {
        $cacheKey = "SnowIO\\AttributeOptionCode\\attributes_supporting_option_codes\\$attributeSetId";

        if ($json = $this->cache->load($cacheKey)) {
            $attributeCodes = \json_decode($json, $assoc = true);
        } else {
            $attributeCodes = $this->findAttributesSupportingOptionCodes($attributeSetId);
            $json = \json_encode($attributeCodes);
            $this->cache->save($json, $cacheKey, [\Magento\Eav\Model\Entity\Attribute::CACHE_TAG], $lifetime = 300);
        }

        return $attributeCodes;
    }

    private function findAttributesSupportingOptionCodes($attributeSetId)
    {
        $attributes = $this->productAttributeManagement->getAttributes($attributeSetId);
        $attributeCodes = [];
        foreach ($attributes as $attribute) {
            if ($this->attributeSupportsOptionCodes($attribute)) {
                $attributeCodes[] = $attribute->getAttributeCode();
            }
        }
        return $attributeCodes;
    }

    private function attributeSupportsOptionCodes(\Magento\Eav\Api\Data\AttributeInterface $attribute)
    {
        $sourceModel = $attribute->getSourceModel();
        if (null === $sourceModel) {
            return in_array($attribute->getFrontendInput(), ['select', 'multiselect']);
        }
        return $sourceModel === Table::class;
    }
}
