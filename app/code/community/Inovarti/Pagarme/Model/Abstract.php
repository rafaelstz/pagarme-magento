<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Lucas Santos <lucas.santos@pagar.me>
 *
 */
abstract class Inovarti_Pagarme_Model_Abstract
    extends Mage_Payment_Model_Method_Abstract
{
    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const REQUEST_TYPE_AUTH_ONLY    = 'AUTH_ONLY';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';

    private $pagarmeApi;

    public function __construct()
    {
        $this->pagarmeApi = Mage::getModel('pagarme/api');
    }

    protected function _place($payment, $amount, $requestType)
    {
        if ($requestType === self::REQUEST_TYPE_AUTH_ONLY || $requestType === self::REQUEST_TYPE_AUTH_CAPTURE) {
            $customer = Mage::helper('pagarme')->getCustomerInfoFromOrder($payment->getOrder());
            $requestParams = $this->prepareRequestParams($payment, $amount, $requestType, $customer);
            $transaction = $this->charge($requestParams);

            $this->prepareTransaction($transaction, $payment);
            return $this;
        }

        Zend_Debug::dump('CAPTURE_ONLY'); die;
    }

    private function charge($requestParams)
    {
        return $this->pagarmeApi->charge($requestParams);
    }

    private function prepareRequestParams($payment, $amount, $requestType, $customer)
    {
        $requestParams = new Varien_Object();
        $requestParams->setPaymentMethod(Inovarti_Pagarme_Model_Api::PAYMENT_METHOD_CREDITCARD)
             ->setAmount(Mage::helper('pagarme')->formatAmount($amount))
             ->setCardHash($payment->getPagarmeCardHash())
             ->setInstallments($payment->getInstallments())
             ->setCapture($requestType == self::REQUEST_TYPE_AUTH_CAPTURE)
             ->setCustomer($customer);

         if ($this->getConfigData('async')) {
             $data->setAsync(true);
             $data->setPostbackUrl(Mage::getUrl('pagarme/transaction_creditcard/postback'));
         }

        return $requestParams;
    }

    private function prepareTransaction($transaction,$payment)
    {
        $this->checkApiErros($transaction);

        if ($transaction->getStatus() == 'refused') {
            $this->refusedStatus($transaction);
        }

        $payment = $this->preparePaymentMethod($payment,$transaction);
        $payment->setTransactionAdditionalInfo(
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
            array(
              'status' => $transaction->getStatus()
            )
        );

        if ($this->getConfigData('async')) {
            $payment->setIsTransactionPending(true);
        }

      return $this;
    }

    private function preparePaymentMethod($payment,$transaction)
    {
      if ($payment->getPagarmeTransactionId()) {

          $transactionIdSprintf = '%s-%s';
          $transactionId = sprintf(
            $transactionId,
            $payment->getPagarmeTransactionId(),
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE
          );

          $payment->setTransactionId($transactionId)
              ->setParentTransactionId($payment->getParentTransactionId())
              ->setIsTransactionClosed(0);
          return $payment;
      }

      $payment->setCcOwner($transaction->getCardHolderName())
        ->setCcLast4($transaction->getCardLastDigits())
        ->setCcType(Mage::getSingleton('pagarme/source_cctype')->getTypeByBrand($transaction->getCardBrand()))
        ->setPagarmeTransactionId($transaction->getId())
        ->setPagarmeAntifraudScore($transaction->getAntifraudScore())
        ->setTransactionId($transaction->getId())
        ->setIsTransactionClosed(0);

      return $payment;
    }

    private function refusedStatus($transaction)
    {
        $reason = $transaction->getStatusReason();
        Mage::log($this->_wrapGatewayError($reason), null, 'pagarme.log');
        Mage::throwException($this->_wrapGatewayError($reason));
    }

    private function checkApiErros($transaction)
    {
        if (!$transaction->getErrors()) {
          return $this;
        }

        $messages = array();
        foreach ($transaction->getErrors() as $error) {
          $messages[] = $error->getMessage() . '.';
        }

        Mage::log(implode("\n", $messages), null, 'pagarme.log');
        Mage::throwException(implode("\n", $messages));
    }
}
