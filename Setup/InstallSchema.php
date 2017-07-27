<?php namespace SITC\Sublogins\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) //@codingStandardsIgnoreLine
    {
        $setup->startSetup();

        /**
         * Create table 'sitc_sublogins_info'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('sitc_sublogins_info')
        )->addColumn(
            'account_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'prefix',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Prefix'
        )->addColumn(
            'lastname',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Lastname'
        )
            ->addColumn(
                'firstname',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Firstname'
            )
            ->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email'
            )->addColumn(
                'address',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Address'
            )
            ->addColumn(
                'password',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Password'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addColumn(
                'subscribe',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'subscribe'
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('sitc_sublogins_info'),
                    ['prefix'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['prefix'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->setComment(
                'Main Table'
            );

        // Add more columns here

        $setup->getConnection()->createTable($table);
        $table = $setup->getConnection()->newTable(
            $setup->getTable('sitc_sublogins_account')
        )->addColumn(
            'user_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'User Id'
        )->addColumn(
            'active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Active'
        )
            ->addColumn(
                'deactive',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Deactive'
            )->addColumn(
                'max_number_sublogin',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Max Number Sublogin'
            )->addColumn(
                'password',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Password'
            );
        $setup->getConnection()->createTable($table);
        $table = $setup->getConnection()->newTable(
            $setup->getTable('sitc_sublogins_store')
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Store Id'
        )->addColumn(
            'user_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'User Id'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}