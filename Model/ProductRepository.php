<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;
use Magento\Store\Model\Store;
use SnowIO\AttributeOptionCode\Api\Data\ProductInterface;
use SnowIO\AttributeOptionCode\Api\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private $vanillaRepository;
    private $productDataMapper;
    private $storeManager;
    private $productResourceModel;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $vanillaRepository,
        ProductDataMapper $productDataMapper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
    ) {
        $this->vanillaRepository = $vanillaRepository;
        $this->productDataMapper = $productDataMapper;
        $this->storeManager = $storeManager;
        $this->productResourceModel = $productResourceModel;
    }

    public function save(ProductInterface $product, $saveOptions = false)
    {
        if ($this->storeManager->getStore()->getCode() !== Store::ADMIN_CODE) {
            if (!$this->productResourceModel->getIdBySku($product->getSku())) {
                throw new InputException(
                    new Phrase(
                        'Product needs to exist in admin (all) scope before being created in store scope'
                    )
                );
            }
        }

        $this->productDataMapper->replaceOptionCodesWithOptionIds($product);
        $this->vanillaRepository->save($product, $saveOptions);
    }
}
