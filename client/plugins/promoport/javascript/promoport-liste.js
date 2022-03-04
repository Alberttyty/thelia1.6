$(document).ready(function() {
	$('#contenu_int .ligne_claire_rub > li:first-child,#contenu_int .ligne_fonce_rub > li:first-child').each(function(index,element) {
    	if(promoport.indexOf($(element).text()) != -1) $(element).next('li').text('port gratuit');
    });
});

