$(document).ready(function() {
	
	$('a.modifier').click(function (){
		$(this).closest('form').submit();
		return false;
	});
	
	$('a.supprimer').click(function (){
		if(confirm('Voulez-vous vraiment le supprimer ?')){
			return true;		
		}
		return false;
	});
	
});
