<?php
        if(!isset($_SESSION["util"]->id)) exit;

?>

<script type="text/javascript">

$(document).ready(function() {
  $('#pliantinfos').prev('.entete').before($('#revendeur'));
});

</script>

<?php
	include_once(realpath(dirname(__FILE__)) . "/Revendeur.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
?>

<?php
		
	$revendeur = new Revendeur();
  $monproduit = new Produit();
	$monproduit->charger($_REQUEST['ref']);
  $revendeur->produit='';
  $revendeur->charger_produit($monproduit->id);
	
?>

<!-- début du bloc de gestion des déclinaisons libre -->
<div id="revendeur">
  <div class="entete">
  			<div class="titre" style="cursor:pointer" onclick="$('pliantrevendeur').show('slow');">PRIX REVENDEUR</div>
  			<div class="fonction_valider"><a href="#" onclick="envoyer()">VALIDER LES MODIFICATIONS</a></div>
  </div>
  <div class="blocs_pliants_prod" id="pliantrevendeur">		
  
    <ul class="lignesimple">
    <li class="cellule_designation" style="width:190px;">Prix revendeur HT :</li>
    <li class="cellule" style="width:220px;"><input type="text" name="prixrevendeur" value="<?php echo $revendeur->prixrevendeur ?>" class="form_court" /></li>
    </ul>
    
    <ul class="lignesimple">
    <li class="cellule_designation" style="width:190px;">Prix en promo revendeur HT :</li>
    <li class="cellule" style="width:220px;"><input type="text" name="prix2revendeur" value="<?php echo $revendeur->prix2revendeur ?>" class="form_court" /></li>
    </ul>
    
    <ul class="lignesimple">
    <li class="cellule_designation" style="width:190px;">En promotion :</li>
    <li class="cellule" style="width:220px;"><input type="checkbox" name="promorevendeur" value="1" <?php if($revendeur->promorevendeur==1) echo "checked=\"checked\""; ?> /></li>
    </ul>
  
  </div>
  <br/>
</div>
