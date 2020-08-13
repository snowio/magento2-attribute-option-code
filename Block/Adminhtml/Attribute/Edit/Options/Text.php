<?php

namespace SnowIO\AttributeOptionCode\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\Text as MagentoSwatchesText;

class Text extends MagentoSwatchesText
{
    /**
     * @var string
     */
    protected $_template = 'SnowIO_AttributeOptionCode::catalog/product/attribute/text.phtml';
    
}
