<?php

class PagarMe_Core_Transaction_BoletoController extends Mage_Core_Controller_Front_Action
{
    public function postbackAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->getResponse()->setHttpResponseCode(405);
        }

        if (!$this->isValidRequest($request)) {
            return $this->getResponse()->setHttpResponseCode(400);
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

    private function isValidRequest(Mage_Core_Controller_Request_Http $request)
    {
        if ($request->getPost('id') == null) {
            return false;
        }

        if ($request->getPost('current_status') == null) {
            return false;
        }

        $signature = $request->getHeader('X-Hub-Signature');

        if ($signature == false) {
            return false;
        }

        if (!$this->isAuthenticRequest($request, $signature)) {
            return false;
        }

        return true;
    }

    private function isAuthenticRequest(
        Mage_Core_Controller_Request_Http $request,
        $signature
    ) {
        return Mage::getModel('pagarme_core/sdk_adapter')->getPagarMeSdk()
            ->postback()
            ->validateRequest($request->getRawBody(), $signature);
    }
}