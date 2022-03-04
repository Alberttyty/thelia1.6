<?php
preg_match("`([^\/]*).php`",$_SERVER['PHP_SELF'],$page);
if($page[1] == "contenu_modifier"){  
  $type="contenu";
}
?>

<script type="text/javascript">

function charger_listproduitsassocies(rubrique){
	$('#select_prodasso').load(
		'/client/plugins/produitsassocies/ajax/produitsassocies.php',
		'action=produit&id_rubrique=' + rubrique + '&id_objet=<?php echo $_GET['id'] ?>&type=<?php echo $type ?>'
	);                                                     
}

function produitsassocies_ajouter(id){
	if (id)
		$('#produitsassocies_liste').load(
			'/client/plugins/produitsassocies/ajax/produitsassocies.php',
			'action=ajouter&id='+ id + '&id_objet=<?php echo $_GET['id'] ?>&type=<?php echo $type ?>',
			function(){
				charger_listacc($('#accessoire_rubrique').val());
			}
		);
}

function produitsassocies_supprimer(id){
	$('#produitsassocies_liste').load(
		'/client/plugins/produitsassocies/ajax/produitsassocies.php',
		'action=supprimer&id='+ id + '&id_objet=<?php echo $_GET['id'] ?>&type=<?php echo $type ?>',
		function(){
			charger_listproduitsassocies($('#produitsassocies_rubrique').val());
		}
	);
}

</script>