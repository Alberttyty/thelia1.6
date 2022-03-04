$(document).ready(function() {

	testPromoRubrique();
	$('input[name="promorubrique_actif"]').change(function (){testPromoRubrique();});
	
});

/* Promo rubrique est utilisé ? */
function testPromoRubrique(){
	if($('#promorubrique_actif_oui').is(':checked')){
		$('#promorubrique').addClass('actif');
		$('#promorubrique').removeClass('inactif');
	}
	else {
		$('#promorubrique').addClass('inactif');
		$('#promorubrique').removeClass('actif');
		$('#promorubrique .choix input').attr('checked',false);
	}
}