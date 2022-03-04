function declibre_supprimer(id){
	if(confirm("Validez vous cette suppression ?")){
		var lang=$('input[name="lang"]').val();
		$.ajax({type:'GET', cache:false, url:'../client/plugins/declibre/gestdeclibre.php', data:'action=declibresupprimer&id=' + id + '&lang='+lang, success:function(html){$('#declibre_liste').html(html); declibre_rafraichir();}})
	}
}

function declibre_supprimertout(ref){
	if(confirm("Voulez vous tout supprimer ?")){
		var lang=$('input[name="lang"]').val();
		$.ajax({type:'GET', cache:false, url:'../client/plugins/declibre/gestdeclibre.php', data:'action=declibresupprimertout&ref=' + ref + '&lang='+lang, success:function(html){$('#declibre_liste').html(html); declibre_rafraichir();}})
	}
}

function declibre_ajouter(ref){
	var lang=$('input[name="lang"]').val();
	$.ajax({type:'GET', cache:false, url:'../client/plugins/declibre/gestdeclibre.php', data:'action=declibreajouter&ref=' + ref + '&lang='+lang, success:function(html){$('#declibre_liste').html(html)}})
}

function declibre_combinaisons(ref){
  var lang=$('input[name="lang"]').val();
  var combinaison_1=$('#declibrecombinaison_1').val();
  var combinaison_2=$('#declibrecombinaison_2').val();
  combinaison_1=combinaison_1.replace("&", "");
  combinaison_2=combinaison_2.replace("&", "");
  if(combinaison_1==""||combinaison_2=="") {alert("Combinaisons vides");return false;}
  if(confirm("Créer les combinaisons possible pour : \n\n"+combinaison_1+"\n\n"+combinaison_2))
	$.ajax({type:'GET', cache:false, url:'../client/plugins/declibre/gestdeclibre.php', data:'action=declibrecombinaisons&ref=' + ref + '&combinaison_1=' + combinaison_1 + '&combinaison_2=' + combinaison_2 + '&lang='+lang, success:function(html){$('#declibre_liste').html(html)}})
}

$(document).ready(function() {
  
  $('#declibre').insertAfter('#pliantcaracteristiques');
  $('#declibre').not(':first').remove();
  
});  