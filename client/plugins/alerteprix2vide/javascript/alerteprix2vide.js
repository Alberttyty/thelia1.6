$(document).ready(function(){
	
	$('input[name="promo[]"]').change(function () {
		if ($(this).is(":checked")&&($(this).closest('li').prev().find('span').text()=='0')) {
			alert('Attention, le produit sélectionné à un prix promo égal à 0.');	
		}
	});
	
	$('input#promo').change(function () {	
		if ($(this).is(":checked")&&($(this).closest('form').find('input#prix2').val()=='0' || $(this).closest('form').find('input#prix2').val()=='')) {
			alert('Attention, le prix promo est égal à 0.');	
		}
	});
	
});