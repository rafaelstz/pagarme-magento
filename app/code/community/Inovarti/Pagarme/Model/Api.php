<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Model_Api
{
	const VERSION 	= '1';
	const ENDPOINT 	= 'https://api.pagar.me';

	const PAYMENT_METHOD_BOLETO = 'boleto';
	const PAYMENT_METHOD_CREDITCARD = 'credit_card';

	const TRANSACTION_STATUS_PROCESSING = 'processing';
	const TRANSACTION_STATUS_AUTHORIZED = 'authorized';
	const TRANSACTION_STATUS_PAID = 'paid';
	const TRANSACTION_STATUS_WAITING_PAYMENT = 'waiting_payment';
	const TRANSACTION_STATUS_REFUSED = 'refused';
	const TRANSACTION_STATUS_REFUNDED = 'refunded';


	protected $_apiKey;

	public function setApiKey($key)
	{
		$this->_apiKey = $key;
		return $this;
	}

	public function getApiKey()
	{
		if (!$this->_apiKey) {
			throw new Exception("You need to configure API key before performing requests.");
		}
		return $this->_apiKey;
	}

	public function charge(Varien_Object $data)
	{
		$data->setApiKey($this->getApiKey());
		$response = $this->request(
			$this->getTransactionUrl(),
			$this->parseArray($data),
			Zend_Http_Client::POST
		);
		return $response;
	}

	public function capture($id)
	{
		$data = new Varien_Object();
		$data->setApiKey($this->getApiKey());
		$response = $this->request(
			$this->getTransactionCaptureUrl($id),
			$this->parseArray($data),
			Zend_Http_Client::POST
		);
		return $response;
	}

	public function refund($id)
	{
		$data = new Varien_Object();
		$data->setApiKey($this->getApiKey());
		$response = $this->request(
			$this->getTransactionRefundUrl($id),
			$this->parseArray($data),
			Zend_Http_Client::POST
		);
		return $response;
	}

	public function find($id)
	{
		$data = new Varien_Object();
		$data->setApiKey($this->getApiKey());
		$response = $this->request(
			$this->getTransactionUrl($id),
			$this->parseArray($data),
			Zend_Http_Client::GET
		);
		return $response;
	}

	public function request($url, $data = array(), $method='GET')
	{
		$client = new Varien_Http_Client($url, array('timeout'	=> 30));
		$client->setMethod($method);
		if ($method == Zend_Http_Client::POST) {
			$client->setParameterPost($data);
		} else {
			$client->setParameterGet($data);
		}

		$response = $client->request();
		$body = json_decode($response->getBody(), true);
		$result = $this->parseObject($body);
		return $result;
	}

	public function parseObject(array $data)
	{
		$object = new Varien_Object();
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				if ($this->_isAssoc($value)) {
					$object->setData($key, $this->parseObject($value));
				} else {
					$items = array();
					foreach ($value as $itemKey => $itemValue) {
						$items[$itemKey] = $this->parseObject($itemValue);
					}
					$object->setData($key, $items);
				}
			} else {
				$object->setData($key, $value);
			}
		}
		return $object;
	}

	public function parseArray(Varien_Object $object)
	{
		$array = array();
		foreach ($object->getData() as $key => $value) {
			if ($value instanceof Varien_Object) {
				$array[$key] = $this->parseArray($value);
			} elseif (is_array($value)) {
				$items = array();
				foreach ($value as $itemKey => $itemValue) {
					if ($itemValue instanceof Varien_Object) {
						$items[$itemKey] = $this->parseArray($itemValue);
					} else {
						$items[$itemKey] = $itemValue;
					}
				}
				$array[$key] = $items;
			} else {
				$array[$key] = $value;
			}
		}
		return $array;
	}

	public function validateFingerprint($id, $fingerprint)
	{
		$isValid = sha1($id . '#' . $this->getApiKey()) == $fingerprint;
		return $isValid;
	}

	public function getBaseUrl()
	{
		$url = self::ENDPOINT . '/' . self::VERSION;
		return $url;
	}

	public function getTransactionUrl($id=null)
	{
		$url = $this->getBaseUrl() . '/transactions';
		if ($id) {
			$url .= '/' . $id;
		}
		return $url;
	}

	public function getTransactionCaptureUrl($id)
	{
		$url = $this->getBaseUrl() . '/transactions/' . $id . '/capture';
		return $url;
	}

	public function getTransactionRefundUrl($id)
	{
		$url = $this->getBaseUrl() . '/transactions/' . $id . '/refund';
		return $url;
	}

	public function getTransactionCardhashUrl()
	{
		$url = $this->getBaseUrl() . '/transactions/card_hash_key';
		return $url;
	}

	public function getCustomerUrl($id=null)
	{
		$url = $this->getBaseUrl() . '/customers';
		if ($id) {
			$url .= '/' . $id;
		}
		return $url;
	}

	protected function _isAssoc($array) {
	  return (bool)count(array_filter(array_keys($array), 'is_string'));
	}
}
