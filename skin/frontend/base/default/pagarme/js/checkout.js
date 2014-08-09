/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

Payment.prototype._save = Payment.prototype.save;
Payment.prototype.save = function() {
    this.savePagarmeCcData();
    this._save();
};

Payment.prototype.savePagarmeCcData = function() {
    if (this.currentMethod != 'pagarme_cc') return;
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

Review.prototype._save = Review.prototype.save;
Review.prototype.save = function() {
    if (payment.currentMethod == 'pagarme_cc') {
        var creditCard = new PagarMe.creditCard();
        creditCard.cardHolderName = payment.pagarmeCcData.cc_owner;
        creditCard.cardExpirationMonth = payment.pagarmeCcData.expiration;
        creditCard.cardExpirationYear = payment.pagarmeCcData.expiration_yr;
        creditCard.cardNumber = payment.pagarmeCcData.cc_number;
        creditCard.cardCVV = payment.pagarmeCcData.cc_cid;

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
