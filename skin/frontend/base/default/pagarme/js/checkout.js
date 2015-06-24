/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

function pagarmeDisableAll(element){
    $$(element).each(function(obj){
        $(obj).disable();
        $(obj).setStyle({background: 'red'});
    });
}

document.observe("dom:loaded",function(){

if (typeof OSCPayment !== "undefined") {
    // One Step Checkout Brasil 6 Pro
    OSCForm.disablePlaceOrderButton = function() {
        pagarmeDisableAll ('div#onestepcheckout-place-order button');
    }
    OSCPayment._savePayment = OSCPayment.savePayment;
    OSCPayment.savePayment = function() {
        if (OSCForm.validate()) {
            if (OSCPayment.currentMethod == 'pagarme_cc') {
                var creditCard = new PagarMe.creditCard();
                creditCard.cardHolderName = $(OSCPayment.currentMethod+'_cc_owner').value;
                creditCard.cardExpirationMonth = $(OSCPayment.currentMethod+'_expiration').value;
                creditCard.cardExpirationYear = $(OSCPayment.currentMethod+'_expiration_yr').value;
                creditCard.cardNumber = $(OSCPayment.currentMethod+'_cc_number').value;
                creditCard.cardCVV = $(OSCPayment.currentMethod+'_cc_cid').value;

                creditCard.generateHash(function(cardHash) {
                    $(OSCPayment.currentMethod+'_pagarme_card_hash').value = cardHash;
                    this._savePayment();
                }.bind(this));
            } else {
                this._savePayment();
            }
        }
    }
} else if (typeof OPC !== "undefined") {
    // One Step Checkout Brasil
    OPC.prototype._save = OPC.prototype.save;
    OPC.prototype.save = function() {
        if (checkout.loadWaiting!=false) return;
        if (this.validator.validate()) {
            if (payment.currentMethod == 'pagarme_cc') {
                var creditCard = new PagarMe.creditCard();
                creditCard.cardHolderName = $(payment.currentMethod+'_cc_owner').value;
                creditCard.cardExpirationMonth = $(payment.currentMethod+'_expiration').value;
                creditCard.cardExpirationYear = $(payment.currentMethod+'_expiration_yr').value;
                creditCard.cardNumber = $(payment.currentMethod+'_cc_number').value;
                creditCard.cardCVV = $(payment.currentMethod+'_cc_cid').value;

                $('review-please-wait').show();
                creditCard.generateHash(function(cardHash) {
                    $('review-please-wait').hide();
                    $(payment.currentMethod+'_pagarme_card_hash').value = cardHash;
                    this._save();
                }.bind(this));
            } else {
                this._save();
            }
        }
    }
} else if(typeof IWD !== "undefined" && typeof IWD.OPC !== "undefined") {
    // One Page Checkout by IWD
    IWD.OPC._saveOrder = IWD.OPC.saveOrder;
    IWD.OPC.saveOrder = function() {
        if (payment.currentMethod == 'pagarme_cc') {
            var creditCard = new PagarMe.creditCard();
            creditCard.cardHolderName = $(payment.currentMethod+'_cc_owner').value;
            creditCard.cardExpirationMonth = $(payment.currentMethod+'_expiration').value;
            creditCard.cardExpirationYear = $(payment.currentMethod+'_expiration_yr').value;
            creditCard.cardNumber = $(payment.currentMethod+'_cc_number').value;
            creditCard.cardCVV = $(payment.currentMethod+'_cc_cid').value;

            IWD.OPC.Checkout.showLoader();
            IWD.OPC.Checkout.lockPlaceOrder();
            creditCard.generateHash(function(cardHash) {
                IWD.OPC.Checkout.hideLoader();
                IWD.OPC.Checkout.unlockPlaceOrder();
                $(payment.currentMethod+'_pagarme_card_hash').value = cardHash;
                this._saveOrder();
            }.bind(this));
        } else {
            this._saveOrder();
        }
    }
} else if (typeof AWOnestepcheckoutForm !== "undefined") {
    //One Step Checkout by aheadWorks
    AWOnestepcheckoutForm.prototype._placeOrder = AWOnestepcheckoutForm.prototype.placeOrder;
    AWOnestepcheckoutForm.prototype.placeOrder = function() {
        if (this.validate()) {
            if (awOSCPayment.currentMethod == 'pagarme_cc') {
                var creditCard = new PagarMe.creditCard();
                creditCard.cardHolderName = $(awOSCPayment.currentMethod+'_cc_owner').value;
                creditCard.cardExpirationMonth = $(awOSCPayment.currentMethod+'_expiration').value;
                creditCard.cardExpirationYear = $(awOSCPayment.currentMethod+'_expiration_yr').value;
                creditCard.cardNumber = $(awOSCPayment.currentMethod+'_cc_number').value;
                creditCard.cardCVV = $(awOSCPayment.currentMethod+'_cc_cid').value;

                this.showOverlay();
                this.showPleaseWaitNotice();
                this.disablePlaceOrderButton();
                creditCard.generateHash(function(cardHash) {
                    this.enablePlaceOrderButton();
                    this.hidePleaseWaitNotice();
                    this.hideOverlay();
                    $(awOSCPayment.currentMethod+'_pagarme_card_hash').value = cardHash;
                    this._placeOrder();
                }.bind(this));
            } else {
                this._placeOrder();
            }
        }
    }
} else {
    // Default Magento Checkout
    Payment.prototype._disable = function() {
        pagarmeDisableAll ('div#payment-buttons-container button');
    }
    Payment.prototype._save = Payment.prototype.save;
    Payment.prototype.save = function() {
        if (checkout.loadWaiting!=false) return;
        var validator = new Validation(this.form);
        if (this.validate() && validator.validate()) {
            if (this.currentMethod == 'pagarme_cc') {
                this.pagarme_cc_data = {};
                var fields = ['installments', 'cc_type', 'cc_number', 'cc_owner', 'expiration', 'expiration_yr', 'cc_cid'];
                fields.each(function(field){
                    this.pagarme_cc_data[field] = $(this.currentMethod+'_'+field).value;
                }.bind(this));
            } else {
                this.pagarme_cc_data = null; //clear data
            }
            this._disable();
            this._save();
        }
    };

    Review.prototype._disable = function() {
        pagarmeDisableAll ('div#review-buttons-container button');
    }
    Review.prototype._save = Review.prototype.save;
    Review.prototype.save = function() {
        if (payment.currentMethod == 'pagarme_cc') {
            var creditCard = new PagarMe.creditCard();
            creditCard.cardHolderName = $(payment.currentMethod+'_cc_owner').value;
            creditCard.cardExpirationMonth = $(payment.currentMethod+'_expiration').value;
            creditCard.cardExpirationYear = $(payment.currentMethod+'_expiration_yr').value;
            creditCard.cardNumber = $(payment.currentMethod+'_cc_number').value;
            creditCard.cardCVV = $(payment.currentMethod+'_cc_cid').value;

            checkout.setLoadWaiting('review');
            creditCard.generateHash(function(cardHash) {
                checkout.setLoadWaiting(false);
                $(payment.currentMethod+'_pagarme_card_hash').value = cardHash;
                this._disable();
                this._save();
            }.bind(this));
        } else {
            this._disable();
            this._save();
        }
    }
}

});

