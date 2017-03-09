<?php

class PagarMe_Core_Model_Entity_CreditCard implements
    PagarMe_Core_Model_Entity_EntityInterface
{
    /**
     * @var float
     */
    protected $_amount;
    /**
     * @var string
     */
    protected $_postBackUrl;
    /**
     * @var array
     */
    protected $_metadata = [];
    /**
     * @var string
     */
    protected $_token;

    const PAGARME_PAYMENT_METHOD = 'pagarme_checkout_credit_card';
    
    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return self::PAGARME_PAYMENT_METHOD;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * @return bool
     */
    public function getCapture()
    {
        return $this->_capture;
    }

    /**
     * @return string
     */
    public function getPostBackUrl()
    {
        return $this->_postBackUrl;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * @param float|int $amount
     *
     * @return void
     */
    public function setAmount($amount)
    {
        $this->_amount = $amount;
    }

    /**
     * @param bool $capture
     *
     * @return void
     */
    public function setCapture($capture)
    {
        $this->_capture = $capture;
    }

    /**
     * @param string $postBackUrl
     *
     * @return void
     */
    public function setPostBackUrl($postBackUrl)
    {
        $this->_postBackUrl = $postBackUrl;
    }

    /**
     * @param array $metadata
     *
     * @return void
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
    }

    /**
     * @param string $token
     *
     * @return void
     */
    public function setToken($token)
    {
        $this->_token = $token;
    }
}
