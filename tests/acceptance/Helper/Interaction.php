<?php

namespace PagarMe\Magento\Test\Helper;

trait Interaction
{
    public function waitForElement($element, $timeout)
    {
        $this->getSession()->wait(
            $timeout,
            "document.querySelector('${element}').style.display != 'none'"
        );
    }
}
