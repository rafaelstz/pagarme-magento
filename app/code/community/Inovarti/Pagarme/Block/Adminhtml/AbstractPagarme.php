<?php

abstract class Inovarti_Pagarme_Block_Adminhtml_AbstractPagarme
    extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * @return $this
     * @throws Exception
     */
    protected function prepareCollection()
    {
        $accounts = PagarMe_Bank_Account::all(10, 0);

        $collection = Mage::getModel('pagarme/ServiceVarienDataCollection');

        foreach ($accounts as $account) {

            $accountObject = new Varien_Object();
            $accountObject->setId($account->getId());
            $accountObject->setBankCode($account->getBankCode());
            $accountObject->setAgency($account->getAgencia());
            $accountObject->setAgencyDv($account->getAgenciaDv());
            $accountObject->setAccount($account->getConta());
            $accountObject->setAccountDv($account->getContaDv());
            $accountObject->setDocumentType($account->getDocumentType());
            $accountObject->setDocumentNumber($account->getDocumentNumber());
            $accountObject->setLegalName($account->getLegalName());
            $accountObject->setChargeTransferFees($account->getChargeTransferFees());
            $accountObject->setDateCreated($account->getDateCreated());

            $collection->addItem($accountObject);
        }

        $this->banksCollection = $collection;
        return $this;
    }
}