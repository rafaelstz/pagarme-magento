<?php

$installer = new Mage_Catalog_Model_Resource_Setup('pagarme_setup');
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('pagarme_recipients'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('transfer_interval', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
    ), 'Frequency at which the receiver will be paid.')
    ->addColumn('transfer_day', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Day on which the recipient will be paid.')
    ->addColumn('transfer_enabled', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Variable indicating whether the recipient can receive payments automatically')
    ->addColumn('bank_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Agency verify digit')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, array(
        'nullable'  => false,
    ), 'date time created row')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, array(
        'nullable'  => false,
    ), 'date time updated row');

$installer->getConnection()->createTable($table);
$installer->endSetup();