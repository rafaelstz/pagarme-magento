/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

Payment.prototype._save = Payment.prototype.save;
Payment.prototype.save = function() {
    if (this.currentMethod == 'pagarme_cc') {
        if (checkout.loadWaiting!=false) return;
        var validator = new Validation(this.form);
        if (this.validate() && validator.validate()) {
            checkout.setLoadWaiting('payment');
            this.savePagarmeCcData();
            this.generateCardHash();
        }
    } else {
        this._save();
    }
};

Payment.prototype.generateCardHash = function(save) {
    var creditCard = new PagarMe.creditCard();
    creditCard.cardHolderName = $(this.currentMethod+'_cc_owner').value;
    creditCard.cardExpirationMonth = $(this.currentMethod+'_expiration').value;
    creditCard.cardExpirationYear = $(this.currentMethod+'_expiration_yr').value;
    creditCard.cardNumber = $(this.currentMethod+'_cc_number').value;
    creditCard.cardCVV = $(this.currentMethod+'_cc_cid').value;
    creditCard.generateHash(function(cardHash) {
        $(this.currentMethod+'_pagarme_card_hash').value = cardHash;
        var request = new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serialize(this.form)
            }
        );
    }.bind(this));
};

Payment.prototype.savePagarmeCcData = function() {
    var fields = ['cc_type', 'cc_number', 'cc_owner', 'expiration', 'expiration_yr', 'cc_cid'];
    this.pagarmeCcData = {};
    fields.each(function(field){
        this.pagarmeCcData[field] = $(this.currentMethod+'_'+field).value;
    }.bind(this));
};

Payment.prototype.loadPagarmeCcData = function() {
    if (this.currentMethod == 'pagarme_cc' && this.pagarmeCcData) {
        $H(this.pagarmeCcData).each(function(field){
            $(this.currentMethod+'_'+field.key).value = field.value;
        }.bind(this));
    }
};
