$(document).ready(function() {
	$('#promoport_oui').appendTo($('input[name="type"]:first').closest('li'));
	$('#promoport').insertAfter($('input[name="type"]:first').closest('ul'));
	testPromoPortOui();
	$('input[name="type"]').change(function (){
		testPromoPortOui();
	});
});

/* Promo pour les ports est choisie ? */
function testPromoPortOui(){
	if($('#promoport_oui input').is(':checked')){
		$('#promoport').addClass('actif');
		$('#promoport').removeClass('inactif');
		$('input[name="type"]').not(':last').removeAttr('checked');
		$('input[name="valeur"]').val('0');
		$('input[name="valeur"]').closest('ul').hide();
		var nochecked=true;
		$('#promoport .transporteurs input,#promoport .zones input').each(function(index,element) {
            if($(element).is(':checked')) nochecked=false;
        });
		if(nochecked) $('#promoport .transporteurs input,#promoport .zones input').attr('checked',true);
	}
	else {
		$('#promoport').addClass('inactif');
		$('#promoport').removeClass('actif');
		$('input[name="valeur"]').closest('ul').show();
		$('#promoport .transporteurs input').attr('checked',false);
		$('#promoport .zones input').attr('checked',false);
	}
}
