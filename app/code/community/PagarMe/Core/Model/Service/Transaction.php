<?php

use PagarMe\Sdk\Transaction\CreditCardTransaction;
use PagarMe\Sdk\Transaction\BoletoTransaction;

class PagarMe_Core_Model_Service_Transaction
{
    /**
     * @var \PagarMe\Sdk\PagarMe
     */
    protected $pagarMeSdk;
    
    /**
     * @return \PagarMe\Sdk\PagarMe
     */
    public function getPagarMeSdk()
    {
        if (is_null($this->pagarMeSdk)) {
            $this->setPagarMeSdk(
                Mage::getModel('pagarme_core/sdk_adapter')
                    ->getPagarMeSdk()
            );
        }

        return $this->pagarMeSdk;
    }

    /**
     * @param \PagarMe\Sdk\PagarMe $pagarMeSdk
     *
     * @return void
     */
    public function setPagarMeSdk(\PagarMe\Sdk\PagarMe $pagarMeSdk)
    {
        $this->pagarMeSdk = $pagarMeSdk;
    }

    /**
     * @param PagarMe_Core_Model_Entity_EntityInterface $entity
     *
     * @return CreditCardTransaction|BoletoTransaction
     *
     * @throws Exception
     */
    public function capture(
        PagarMe_Core_Model_Entity_EntityInterface $entity
    ) {
        $transaction = $this->getTransactionObject($entity);

        try {
            return $this->getPagarMeSdk()
                ->transaction()
                ->capture(
                    $transaction,
                    $entity->getAmount()
                );
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param PagarMe_Core_Model_Entity_EntityInterface $entity
     *
     * @return CreditCardTransaction|BoletoTransaction
     *
     * @throws Exception
     */
    private function getTransactionObject($entity)
    {
        $paymentMethod = $entity->getPaymentMethod();

        if ($paymentMethod === PagarMe_Core_Model_Entity_CreditCard::PAGARME_PAYMENT_METHOD) {
            return new CreditCardTransaction([
                'token' => $entity->getToken()
            ]);
        }

        if ($paymentMethod === PagarMe_Core_Model_Entity_Boleto::PAGARME_PAYMENT_METHOD) {
            return new BoletoTransaction([
                'token' => $entity->getToken()
            ]);
        }

        throw new Exception('Unsupported payment method: '.$paymentMethod);
    }
}
