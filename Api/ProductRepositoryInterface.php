<?php
namespace SnowIO\AttributeOptionCode\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;

interface ProductRepositoryInterface
{
    /**
     * Create product
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws InputException
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function save(ProductInterface $product, $saveOptions = false);
}
