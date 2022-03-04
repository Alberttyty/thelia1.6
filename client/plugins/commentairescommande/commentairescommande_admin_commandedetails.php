<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("commentairescommande");
include_once(realpath(dirname(__FILE__)) . "/Commentairescommande.class.php");
  
$commande = new Commande();
$commande->charger_ref($_REQUEST['ref']);
  
$commentaires = new Commentairescommande();
$existe=$commentaires->charger_commande($commande->id);
  
if($_REQUEST['action']=="commentairescommande"){
  
	$commentaires->id_commande=$commande->id;
    $commentaires->texte=$_REQUEST['texte']; 
    if($existe) $commentaires->maj();
    else $commentaires->add();
     
}  
?>     

<form action="commande_details.php" method="post">
<div class="bordure_bottom" id="commentaires">
  <div class="entete_liste_client">
  	<div class="titre">COMMENTAIRES</div>
  </div>
  <ul class="ligne_claire_BlocDescription" style="background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
		<li><textarea name="texte"><?php echo $commentaires->texte; ?></textarea><input type="submit" class="valider" value="Valider"/></li>
	</ul>
  <input type="hidden" name="ref" value="<?php echo $_REQUEST['ref']; ?>"/>
  <input type="hidden" name="action" value="commentairescommande"/>  
</div>
</form>