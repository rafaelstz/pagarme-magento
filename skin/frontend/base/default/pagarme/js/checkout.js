/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

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
        creditCard.cardHolderName = payment.pagarme_cc_data.cc_owner;
        creditCard.cardExpirationMonth = payment.pagarme_cc_data.expiration;
        creditCard.cardExpirationYear = payment.pagarme_cc_data.expiration_yr;
        creditCard.cardNumber = payment.pagarme_cc_data.cc_number;
        creditCard.cardCVV = payment.pagarme_cc_data.cc_cid;

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
