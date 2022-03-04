$(document).ready(function() {
	$('.date')
		.datePicker({createButton:false})
		.bind(
			'click',
			function()
			{
				$(this).dpDisplay();
				this.blur();
				return false;
			}
		);
});

function supprimerFichier(id){
	$.post( "/gestion/module.php",{ 'id': id, 'nom': 'revuedepresse', 'action': 'supprimer_fichier'}, function(data) {
		if(data.indexOf("ok_suppression_fichier_revue_de_presse") > -1){$('#lien_fichier').html('');}
	})
}