<?php

class PagarMe_Core_Transaction_BoletoController extends Mage_Core_Controller_Front_Action
{
    public function postbackAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->getResponse()
                ->setHttpResponseCode(405);
        }

        $transactionId = $request->getPost('id');
        $currentStatus = $request->getPost('current_status');

        try {
            Mage::getModel('pagarme_core/postback_boleto')
                ->processPostback(
                    $transactionId,
                    $currentStatus
                );
            return $this->getResponse()
                ->setBody('ok');
        } catch (Exception $e) {
            return $this->getResponse()
                ->setHttpResponseCode(500)
                ->setBody($e->getMessage());
        }
    }
}