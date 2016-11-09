<?php
namespace SnowIO\AttributeOptionCode\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\ResourceModel\Db\Context;

class AttributeOptionCodeRepository
{
    private $dbConnection;
    
    public function __construct(Context $dbContext, $connectionName = null)
    {
        $connectionName = $connectionName ?: ResourceConnection::DEFAULT_CONNECTION;
        $this->dbConnection = $dbContext->getResources()->getConnection($connectionName);
    }

    public function getOptionId($entityType, $attributeCode, $optionCode)
    {
        
    }

    public function setOptionId($entityType, $attributeCode, $optionCode, $optionId)
    {

    }

    public function removeOption($entityType, $attributeCode, $optionCode, $optionId)
    {

    }

    public function getMaxOptionId($entityType, $attributeCode)
    {

    }
}
