<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE " . $this->getTable('sales/quote_address') . " ADD fee_amount DECIMAL(10, 2) NOT NULL;
    ALTER TABLE " . $this->getTable('sales/quote_address') . " ADD base_fee_amount DECIMAL(10, 2) NOT NULL;
");

$installer->run("
    ALTER TABLE " . $this->getTable('sales/order') . " ADD fee_amount DECIMAL(10,2) NOT NULL;
    ALTER TABLE " . $this->getTable('sales/order') . " ADD base_fee_amount DECIMAL(10,2) NOT NULL;
");

$installer->run("
    ALTER TABLE  " . $this->getTable('sales/order') . " ADD fee_amount_invoiced DECIMAL(10, 2) NOT NULL;
    ALTER TABLE  " . $this->getTable('sales/order') . " ADD base_fee_amount_invoiced DECIMAL(10, 2) NOT NULL;
");

$installer->run("
    ALTER TABLE " . $this->getTable('sales/order') . " ADD fee_amount_refunded DECIMAL(10, 2) NOT NULL;
    ALTER TABLE " . $this->getTable('sales/order') . " ADD base_fee_amount_refunded DECIMAL(10, 2) NOT NULL;
");

$installer->run("
    ALTER TABLE  " . $this->getTable('sales/invoice') . " ADD fee_amount DECIMAL(10, 2) NOT NULL;
    ALTER TABLE  " . $this->getTable('sales/invoice') . " ADD base_fee_amount DECIMAL(10, 2) NOT NULL;
");

$installer->run("
    ALTER TABLE " . $this->getTable('sales/creditmemo') . " ADD fee_amount DECIMAL(10, 2) NOT NULL;
    ALTER TABLE " . $this->getTable('sales/creditmemo') . " ADD base_fee_amount DECIMAL(10, 2) NOT NULL;
");

$installer->endSetup();

