<?php
namespace SnowIO\AttributeOptionCodes\Model;

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
        $tableName = $this->dbConnection->getTableName('option_codes');
        $result = $this->dbConnection->query(
            "SELECT option_id FROM $tableName WHERE option_code = ? AND attribute_code = ? AND entity_type = ?;",
            [
                $optionCode,
                $attributeCode,
                $entityType
            ]
        )->fetchColumn(0);

        if (!$result) {
            return null;
        }
        return (int)$result;
    }

    public function setOptionId($entityType, $attributeCode, $optionCode, $optionId)
    {
        $tableName = $this->dbConnection->getTableName('option_codes');
        $this->dbConnection->query(
            "INSERT INTO $tableName (entity_type, attribute_code, option_code, option_id) VALUES (?, ?, ?, ?);",
            [
                $entityType,
                $attributeCode,
                $optionCode,
                $optionId
            ]
        );
    }

    public function removeOption($entityType, $attributeCode, $optionCode, $optionId)
    {
        $tableName = $this->dbConnection->getTableName('option_codes');

        $this->dbConnection->query(
            "DELETE FROM $tableName WHERE option_code = ? AND attribute_code = ? AND entity_type = ? AND option_id = ?;",
            [
                $optionCode,
                $attributeCode,
                $entityType,
                $optionId
            ]
        );
    }

    public function getMaxOptionId($entityType, $attributeCode)
    {
        $attributeOptionTableName = $this->dbConnection->getTableName('eav_attribute_option');
        $eavAttributeTableName = $this->dbConnection->getTableName('eav_attribute');

        $result = $this->dbConnection->query(
            "SELECT a.option_id FROM $attributeOptionTableName a INNER JOIN $eavAttributeTableName b ON b.attribute_id = a.attribute_id WHERE b.entity_type_id = ? AND b.attribute_code = ? ORDER BY a.option_id DESC LIMIT 0, 1;",
            [
                $entityType,
                $attributeCode
            ]
        )->fetchColumn(0);

        if (!$result) {
            return null;
        }

        return (int)$result;
    }
}
