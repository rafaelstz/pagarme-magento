<?php

abstract class Inovarti_Pagarme_Block_Adminhtml_AbstractPagarme
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function _construct()
    {
        $this->prepareCollection();
    }

    /**
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);

                if (!$data) {
                    return $this;
                }

                $this->getById($data);
                $this->_setFilterValues($data);
                $this->setCollection($this->banksCollection);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function prepareCollection($filter)
    {
        $accounts = PagarMe_Bank_Account::all(10, 0);
        return $this->setCollectionData($accounts);
    }

    /**
     * @param $accounts
     * @param $collection
     * @return $this
     */
    private function setCollectionData($accounts)
    {
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

    /**
     * @param $data
     * @return $this|Inovarti_Pagarme_Block_Adminhtml_AbstractPagarme
     */
    protected function getById($data)
    {
        try {
            $accounts = PagarMe_Bank_Account::findById($data['id']);
        } catch (Exception $e) {
            $collection = Mage::getModel('pagarme/ServiceVarienDataCollection');
            $this->banksCollection = $collection;
            return $this;
        }

        return $this->setCollectionData([$accounts]);
    }
}