<?php

namespace SnowIO\AttributeOptionCode\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 *
 * @package SnowIO\AttributeOptionCode\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Drop foreign key to prevent open code deletion removing value from `option_code` table. Magento deletes then
     * inserts an attribute option rather than update in certain situations, which removes an `option_code`
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $installer = $setup;
            $installer->startSetup();

            $installer->getConnection()->dropForeignKey(
                $installer->getTable('option_code'),
                $installer->getFkName(
                    'option_code',
                    'option_id',
                    'eav_attribute_option',
                    'option_id'
                )
            );

            $installer->endSetup();
        }
    }
}
