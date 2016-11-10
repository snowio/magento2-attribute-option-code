<?php

namespace SnowIO\AttributeOptionCodes\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer
            ->getConnection()
            ->newTable($installer->getTable('option_codes'))
            ->addColumn(
                'entity_type',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Entity Type'
            )->addColumn(
                'attribute_code',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Attribute Code'
            )->addColumn(
                'option_code',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Option Code'
            )->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Option Id'
            );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
