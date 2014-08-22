/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

if (typeof OPC !== "undefined") {
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
} else {
    // Default Magento Checkout
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
            this._save();
        }
    };

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
                this._save();
            }.bind(this));
        } else {
            this._save();
        }
    }
}
