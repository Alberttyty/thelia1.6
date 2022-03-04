$(document).ready(function() {
	
	$("#formulaire").submit(function(event) {
		
		if($("#prix").val()==0){
			alert("Attention : le prix TTC enregistré est égale à 0.");
		}
		
	});
	
});