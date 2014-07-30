<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Model_Source_Cctype extends Mage_Payment_Model_Source_Cctype
{
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE', 'DI', 'JCB', 'EL', 'DC', 'AU', 'HC');
    }

    public function getTypeByBrand($brand)
    {
        $data = array(
            'visa'          => 'VI',
            'mastercard'    => 'MC',
            'amex'          => 'AE',
            'discover'      => 'DI',
            'jcb'           => 'JCB',
            'elo'           => 'EL',
            'diners'        => 'DC',
            'aura'          => 'AU',
            'hipercard'     => 'HC'
        );

        $type = isset($data[$brand]) ? $data[$brand] : null;
        return $type;
    }
}
