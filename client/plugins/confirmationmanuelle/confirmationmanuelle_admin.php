<?php   
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");    

	autorisation("confirmationmanuelle");
  
  require_once(realpath(dirname(__FILE__)) ."/../../../fonctions/divers.php");

	include_once(realpath(dirname(__FILE__)) . "/Confirmationmanuelle.class.php");
	$confirmationmanuelle = new Confirmationmanuelle();
  if(isset($_POST['ref'])){
    if($confirmationmanuelle->confirmer($_POST['ref'])){
      $retour="Commande ".$_POST['ref']." confirmée.";
    }
    else {
      $retour="Erreur : Commande ".$_POST['ref']." introuvable !";
    }
  }
?>     
<div id="contenu_int"> 

  <p align="left">
    <a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=confirmationmanuelle" class="lien04">Confirmation manuelle</a>              
  </p>
                            
  <div id="bloc_description">
    <div class="entete_liste_config">
    	<div class="titre">CONFIRMATION MANUELLE</div>
    	<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit();">VALIDER LES MODIFICATIONS</a></div>
    </div>
    <form name="confirmationmanuelle" class="confirmationmanuelle" id="formulaire" action="module.php?nom=confirmationmanuelle" method="post">
    
    <ul class="ligne_fonce_BlocDescription">
        <li style="width:110px"><input type="hidden" name="action" value="confirmer"/>Référence</li>
        <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="ref" value=""/></li>
    </ul>
         
    <div>
    <?php echo $retour; ?>
    </div>
    
    </form>
    </div>
  </div>
  
</div>