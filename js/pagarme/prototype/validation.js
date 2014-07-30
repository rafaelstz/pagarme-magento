/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

Validation.creditCartTypes.unset('SM'); // SM conflicts with AU
Validation.creditCartTypes.set('DC', [new RegExp('^3(?:0[0-5]|[68][0-9])[0-9]{11}$'), new RegExp('^[0-9]{3}$'), true]);
Validation.creditCartTypes.set('AU', [new RegExp('^50[0-9]{17}$'), new RegExp('^[0-9]{3}$'), true]);
Validation.creditCartTypes.set('EL', [new RegExp('^((((636368)|(438935)|(504175)|(451416)|(636297))[0-9]{0,10})|((5067)|(4576)|(4011))[0-9]{0,12})$'), new RegExp('^([0-9]{3})?$'), true]);
Validation.creditCartTypes.set('HC', [new RegExp('^(606282[0-9]{10}([0-9]{3})?)|(3841[0-9]{15})$'), new RegExp('^([0-9]{3})?$'), false]);
