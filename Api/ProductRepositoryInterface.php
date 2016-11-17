<?php
namespace SnowIO\AttributeOptionCode\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use SnowIO\AttributeOptionCode\Api\Data\ProductInterface;

interface ProductRepositoryInterface
{
    /**
     * Create product
     * @param \SnowIO\AttributeOptionCode\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @return void
     * @throws InputException
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function save(ProductInterface $product, $saveOptions = false);
}
