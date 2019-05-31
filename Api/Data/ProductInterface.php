<?php

namespace SnowIO\AttributeOptionCode\Api\Data;

/**
 * Interface ProductInterface
 *
 * @package SnowIO\AttributeOptionCode\Api\Data
 */
interface ProductInterface extends \Magento\Catalog\Api\Data\ProductInterface
{
    /**
     * @inheritDoc
     *
     * @return \SnowIO\AttributeOptionCode\Api\Data\ProductExtensionInterface|\Magento\Catalog\Api\Data\ProductExtensionInterface|null
     */
    public function getExtensionAttributes();
}
