<?php

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
     * @return void
     */
    public function capture(
        PagarMe_Core_Model_Entity_EntityInterface $entity
    ) {
        switch ($entity->getPaymentMethod()) {
            case PagarMe_Core_Model_Entity_CreditCard::PAGARME_PAYMENT_METHOD:
                return $this->creditCardTransaction($entity);
            case PagarMe_Core_Model_Entity_Boleto::PAGARME_PAYMENT_METHOD:
                return $this->boletoTransaction($entity);
        }
    }

    /**
     * @param PagarMe_Core_Model_Entity_EntityInterface $entity
     *
     * @throws \Exception
     *
     * @return \PagarMe\Sdk\Transaction\CreditCardTransaction
     */
    private function creditCardTransaction(
        PagarMe_Core_Model_Entity_EntityInterface $entity
    ) {
        try {
            $transaction = $this->getPagarMeSdk()
                ->transaction()
                ->get(
                    $entity->getToken()
                );

            return $this->getPagarMeSdk()
                ->transaction()
                ->capture(
                    $transaction,
                    $entity->getAmount()
                );
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param PagarMe_Core_Model_Entity_EntityInterface $entity
     *
     * @throws \Exception
     *
     * @return type
     */
    private function boletoTransaction(
        PagarMe_Core_Model_Entity_EntityInterface $entity
    ) {
        try {
            return $this->getPagarMeSdk()
                ->transaction()
                ->boletoTransaction(
                    $entity->getAmount(),
                    $entity->getCustomer(),
                    $entity->getPostBackUrl(),
                    $entity->getMetadata()
                );
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
