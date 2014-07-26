<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Model_Source_Address {

    protected $_options;

    public function toOptionArray() {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => '', 'label' => Mage::helper('pagarme')->__('Empty')),
                array('value' => 1, 'label' => Mage::helper('pagarme')->__('Street Line %s', 1)),
                array('value' => 2, 'label' => Mage::helper('pagarme')->__('Street Line %s', 2)),
                array('value' => 3, 'label' => Mage::helper('pagarme')->__('Street Line %s', 3)),
                array('value' => 4, 'label' => Mage::helper('pagarme')->__('Street Line %s', 4))
            );
        }
        return $this->_options;
    }

}
