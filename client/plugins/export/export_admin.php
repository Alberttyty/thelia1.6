<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("export");

	include_once(realpath(dirname(__FILE__)) . "/Export.class.php");

  
?>

<div id="contenu_int">

  <p>
  
    <a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=amazone" class="lien04">Amazone</a>
    
  </p>
  
  <div id="bloc_description">
    <div class="entete_liste_config">
    	<div class="titre">EXPORTER LES PRODUIT</div>
    </div>
    <div class="bordure_bottom">
      <ul class="ligne_claire_rub">
      	<li><a href="../client/plugins/export/export_produits.php">Exporter</a></li>
      </ul>
    </div>
  </div>

</div>