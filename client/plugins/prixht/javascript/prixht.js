$(document).ready(function() {
	
	$('input#prix').hide();	
	$('input#prix').parent('th').prev('.designation').text($('input#prixht').parent('td').prev('.designation').text());
	$('input#prix').after($('input#prixht').closest('td').html());
	
	$('input#prix2').hide();
	$('input#prix2').parent('td').prev('.designation').text($('input#prix2ht').parent('td').prev('.designation').text());
	$('input#prix2').after($('input#prix2ht').closest('td').html());
	
	$('.module_prixht').remove();
				   
});