<?php

$installer = new Mage_Catalog_Model_Resource_Setup('pagarme_setup');
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('pagarme_banks'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('bank_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'pagarme bank id')
    ->addColumn('bank_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Bank Code')
    ->addColumn('agency', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Agency account')
    ->addColumn('agency_cv', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Agency verify digit')
    ->addColumn('account_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
    ), 'Account Number')
    ->addColumn('account_cv', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
    ), 'Account verify digit')
    ->addColumn('document_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
    ), 'Document type')
    ->addColumn('document_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
    ), 'Document number')
    ->addColumn('legal_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
    ), 'company name')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, array(
        'nullable'  => false,
    ), 'company name');

$installer->getConnection()->createTable($table);
$installer->endSetup();