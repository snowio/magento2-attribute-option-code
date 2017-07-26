<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\CacheInterface;

class AttributeRepository
{
    private $productAttributeRepository;
    private $searchCriteriaBuilder;
    private $cache;

    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CacheInterface $cache
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cache = $cache;
    }

    public function getAttributesSupportingOptionCodes()
    {
        $cacheKey = \md5(__CLASS__ . '/attributes_supporting_option_codes');

        if ($json = $this->cache->load($cacheKey)) {
            $attributeCodes = \json_decode($json, $assoc = true);
        } else {
            $attributeCodes = $this->findAttributesSupportingOptionCodes();
            $json = \json_encode($attributeCodes);
            $this->cache->save($json, $cacheKey, [\Magento\Eav\Model\Entity\Attribute::CACHE_TAG], $lifetime = 600);
        }

        return $attributeCodes;
    }

    private function findAttributesSupportingOptionCodes()
    {
        return \array_merge(
            $this->getAttributeCodes($this->findSelectAttributes()),
            $this->getAttributeCodes($this->findAttributesWithTableSource())
        );
    }

    private function getAttributeCodes(SearchResultsInterface $searchResults): array
    {
        return \array_map(function (AttributeInterface $attribute) {
            return $attribute->getAttributeCode();
        }, $searchResults->getItems());
    }

    private function findSelectAttributes(): SearchResultsInterface
    {
        $this->searchCriteriaBuilder->create(); // this is the only way to ensure that the builder is empty
        $this->searchCriteriaBuilder->addFilter('frontend_input', ['select', 'multiselect'], 'in');
        $this->searchCriteriaBuilder->addFilter('source_model', null);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        /*
         * ProductAttributeRepositoryInterface::getList() returns an instance of Magento\Framework\Api\SearchResults,
         * not ProductAttributeSearchResultsInterface as the phpdoc states
         */
        return $this->productAttributeRepository->getList($searchCriteria);
    }

    private function findAttributesWithTableSource(): SearchResultsInterface
    {
        $this->searchCriteriaBuilder->create(); // this is the only way to ensure that the builder is empty
        $this->searchCriteriaBuilder->addFilter('source_model', Table::class);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        /*
         * ProductAttributeRepositoryInterface::getList() returns an instance of Magento\Framework\Api\SearchResults,
         * not ProductAttributeSearchResultsInterface as the phpdoc states
         */
        return $this->productAttributeRepository->getList($searchCriteria);
    }
}
