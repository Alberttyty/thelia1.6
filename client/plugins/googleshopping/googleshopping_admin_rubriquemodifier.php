<?php
        if(!isset($_SESSION["util"]->id)) exit;

?>

<script type="text/javascript">

$(document).ready(function() {
  $('#pliantcontenuasso').prev('.entete').before($('#googleshopping'));
});  

</script>

<?php
	include_once(realpath(dirname(__FILE__)) . "/Googleshopping.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubrique.class.php");
?>

<?php
		
	$googleshopping = new Googleshopping();
  $marubrique = new Rubrique();
	$marubrique->charger($_REQUEST['id']);
  $googleshopping->produit='';
  $googleshopping->charger_recursif($marubrique->id);
	
?>

<!-- début du bloc de gestion des déclinaisons libre -->
<div id="googleshopping">
  <div class="entete">
  			<div class="titre" style="cursor:pointer" onclick="$('pliantgoogleshopping').show('slow');">GOOGLE SHOPPING</div>
  			<div class="fonction_valider"><a href="#" onclick="$('#formulaire').submit()">VALIDER LES MODIFICATIONS</a></div>
  </div>
  <div class="blocs_pliants_prod" id="pliantgoogleshopping">		
  
    <ul class="lignesimple">
    <li class="cellule_designation" style="width:140px;">Catégorie de produits Google :</li>
    <li class="cellule" style="width:240px;"><input type="text" name="googleproductcategory" value="<?php echo $googleshopping->googleproductcategory ?>" class="form_long" /></li>
    </ul>
    
    <ul class="lignesimple">
    <li class="cellule" style="width:380px;"><a href="http://www.google.com/basepages/producttype/taxonomy.fr-FR.txt" target="_blank">Liste des catégories disponibles</a></li>
    </ul>
  
  </div>
  <br/>
</div>
