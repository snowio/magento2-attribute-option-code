<?php
namespace SnowIO\AttributeOptionCode\Model;

use SnowIO\AttributeOptionCode\Api\Data\ProductInterface;
use SnowIO\AttributeOptionCode\Api\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private $vanillaRepository;
    private $productDataMapper;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $vanillaRepository,
        ProductDataMapper $productDataMapper
    ) {
        $this->vanillaRepository = $vanillaRepository;
        $this->productDataMapper = $productDataMapper;
    }

    public function save(ProductInterface $product, $saveOptions = false)
    {
        $this->productDataMapper->replaceOptionCodesWithOptionIds($product);
        $this->vanillaRepository->save($product, $saveOptions);
    }
}
