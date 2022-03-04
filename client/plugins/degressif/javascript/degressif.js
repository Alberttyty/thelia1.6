$(document).ready(function() {
	var cible=$("#degressif_liste").closest("form").find("input#prix").closest("table");
  	$("#degressif_liste").insertAfter(cible);
	$('.degressif_ajouter').click(function (){
		var index=parseInt($('#pliantdegressivite table:first tr:visible:last input[type="hidden"]').val())+1;
		if(isNaN(index)) index=0;
		$('#pliantdegressivite table:first tr.valeurs:hidden').clone().show().appendTo('#pliantdegressivite table:first');
		$('#pliantdegressivite table:first tr.valeurs:visible:last').find('input[name="degressif_tranchemin_"]').attr('name','degressif_tranchemin_'+index);
		$('#pliantdegressivite table:first tr.valeurs:visible:last').find('input[name="degressif_tranchemax_"]').attr('name','degressif_tranchemax_'+index);
		$('#pliantdegressivite table:first tr.valeurs:visible:last').find('input[name="degressif_prix_"]').attr('name','degressif_prix_'+index);
		$('#pliantdegressivite table:first tr.valeurs:visible:last').find('input[name="degressif_prix2_"]').attr('name','degressif_prix2_'+index);
		$('#pliantdegressivite table:first tr.valeurs:visible:last').find('input[name="degressif_index_"]').attr('name','degressif_index_'+index).val(index);
		return false;	
	});
});
