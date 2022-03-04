<?php
        if(!isset($_SESSION["util"]->id)) exit;

?>

<script type="text/javascript">

$(document).ready(function() {
  $('#pliantinfos').prev('.entete').before($('#prixachat'));
});

</script>

<?php
	include_once(realpath(dirname(__FILE__)) . "/Prixachat.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
?>

<?php
		
	$prixachat = new Prixachat();
  $monproduit = new Produit();
	$monproduit->charger($_REQUEST['ref']);
  $prixachat->produit='';
  $prixachat->charger_produit($monproduit->id);
	
?>

<!-- début du bloc de gestion des déclinaisons libre -->
<div id="prixachat">
  <div class="entete">
  			<div class="titre" style="cursor:pointer" onclick="$('pliantprixachat').show('slow');">PRIX D'ACHAT</div>
  			<div class="fonction_valider"><a href="#" onclick="envoyer()">VALIDER LES MODIFICATIONS</a></div>
  </div>
  <div class="blocs_pliants_prod" id="pliantprixachat">		
  
    <ul class="lignesimple">
    <li class="cellule_designation" style="width:140px;">Prix d'achat HT :</li>
    <li class="cellule" style="width:240px;"><input type="text" name="prixachat" value="<?php echo $prixachat->prixachat ?>" class="form_court" /></li>
    </ul>
  
  </div>
  <br/>
</div>
