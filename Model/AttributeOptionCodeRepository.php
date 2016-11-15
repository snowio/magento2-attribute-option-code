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
        $select = $this->dbConnection->select()
            ->from(['t' => $this->dbConnection->getTableName('option_code')], 'option_id')
            ->join(['a' => $this->dbConnection->getTableName('eav_attribute')], 'a.attribute_id = t.attribute_id', [])
            ->where('a.attribute_code = ?', $attributeCode)
            ->where('a.entity_type_id = ?', $entityType)
            ->where('t.option_code = ?', $optionCode);

        $result = $this->dbConnection->fetchOne($select);

        return $result ? (int)$result : null;
    }

    public function getOptionCode($entityType, $attributeCode, $optionId)
    {
        $select = $this->dbConnection->select()
            ->from(['t' => $this->dbConnection->getTableName('option_code')], 'option_code')
            ->join(['a' => $this->dbConnection->getTableName('eav_attribute')], 'a.attribute_id = t.attribute_id', [])
            ->where('a.attribute_code = ?', $attributeCode)
            ->where('a.entity_type_id = ?', $entityType)
            ->where('t.option_id = ?', $optionId);

        $result = $this->dbConnection->fetchOne($select);

        return $result ? (int)$result : null;
    }

    public function setOptionId($entityType, $attributeCode, $optionCode, $optionId)
    {
        $tableName = $this->dbConnection->getTableName('option_code');

        $this->dbConnection->insert($tableName, [
            'attribute_id' => $this->getAttributeId($entityType, $attributeCode),
            'option_code' => $optionCode,
            'option_id' => $optionId
        ]);
    }

    public function removeOption($entityType, $attributeCode, $optionCode, $optionId)
    {
        $tableName = $this->dbConnection->getTableName('option_code');

        $this->dbConnection->delete($tableName, [
            'attribute_id' => $this->getAttributeId($entityType, $attributeCode),
            'option_code' => $optionCode,
            'option_id' => $optionId
        ]);
    }

    public function getMaxOptionId($entityType, $attributeCode)
    {
        // we use two selects instead of a join because we're unsure how the join would scale performance-wise
        $attributeId = $this->getAttributeId($entityType, $attributeCode);

        $select = $this->dbConnection->select()
            ->from($this->dbConnection->getTableName('eav_attribute_option'), new \Zend_Db_Expr('MAX(option_id)'))
            ->where('attribute_id = ?', $attributeId);
        $maxOptionId = $this->dbConnection->fetchOne($select);

        if ('' === (string)$maxOptionId) {
            return null;
        }

        return (int)$maxOptionId;
    }

    private function getAttributeId($entityType, $attributeCode)
    {
        $select = $this->dbConnection->select()
            ->from($this->dbConnection->getTableName('eav_attribute'), 'attribute_id')
            ->where('entity_type_id = ?', $entityType)
            ->where('attribute_code = ?', $attributeCode);

        $attributeId = $this->dbConnection->fetchOne($select);

        if ('' === (string)$attributeId) {
            throw new \RuntimeException('The specified attribute does not exist.');
        }

        return (int)$attributeId;
    }
}
