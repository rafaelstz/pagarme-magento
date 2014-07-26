<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

// Quote Payment
$entity = 'quote_payment';
$attributes = array(
	'installments' => array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT)
);

foreach ($attributes as $attribute => $options) {
	$installer->addAttribute($entity, $attribute, $options);
}

// Order Payment
$entity = 'order_payment';
$attributes = array(
	'installments' => array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT),
	'pagarme_transaction_id' => array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER),
	'pagarme_boleto_url' => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
	'pagarme_boleto_barcode' => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
	'pagarme_boleto_expiration_date' => array('type' => Varien_Db_Ddl_Table::TYPE_DATETIME),
	'pagarme_antifraud_score' => array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL)
);

foreach ($attributes as $attribute => $options) {
	$installer->addAttribute($entity, $attribute, $options);
}
