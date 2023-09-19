<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;
use Magento\Store\Model\Store;
use SnowIO\AttributeOptionCode\Api\Data\ProductInterface;
use SnowIO\AttributeOptionCode\Api\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private \Magento\Catalog\Api\ProductRepositoryInterface $vanillaRepository;
    private \SnowIO\AttributeOptionCode\Model\ProductDataMapper $productDataMapper;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \Magento\Catalog\Model\ResourceModel\Product $productResourceModel;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $vanillaRepository,
        ProductDataMapper $productDataMapper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->vanillaRepository = $vanillaRepository;
        $this->productDataMapper = $productDataMapper;
        $this->storeManager = $storeManager;
        $this->productResourceModel = $productResourceModel;
        $this->resourceConnection = $resourceConnection;
    }

    public function save(ProductInterface $product, $saveOptions = false)
    {
        if ($this->storeManager->getStore()->getCode() !== Store::ADMIN_CODE && 
            !$this->productResourceModel->getIdBySku($product->getSku())
        ) {
            throw new InputException(
                new Phrase(
                    'Product needs to exist in admin (all) scope before being created in store scope'
                )
            );
        }

        /**
         * Helps to reduce deadlocks saving products at volume
         *
         * @link https://dev.mysql.com/doc/refman/8.0/en/innodb-transaction-isolation-levels.html
         * @link https://dev.mysql.com/doc/refman/8.0/en/innodb-deadlocks-handling.html
         * @link https://github.com/magento/magento2/blob/15ea8fdeecca7f4164362c8f50fb1ad64245df98/app/code/Magento/Indexer/Model/ResourceModel/AbstractResource.php#L145
         */
        $this->resourceConnection->getConnection()
            ->query('set session transaction isolation level read committed');


        $this->productDataMapper->replaceOptionCodesWithOptionIds($product);
        $this->vanillaRepository->save($product, $saveOptions);
    }
}
