<?php
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");  
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/Triercaracteristiques.class.php");
	autorisation("triercaracteristiques");
?>
<div id="contenu_int"> 
  <p align="left"><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" />Trier les caracteristiques</p>
  <div class="entete_liste_client">
  	<div class="titre">Trier les caracteristiques</div>
  </div>
  <?php
  
  if($_GET['caracteristique']!=""){
  
    $triercaracteristiques=new Triercaracteristiques();  
    $nb=$triercaracteristiques->trier($_GET['caracteristique']);
  
    echo "<p>$nb lignes ont été classées.</p>";
  
  }
  
  ?>
  
  <form action="module.php" name="form_triercaracteristiques" id="form_triercaracteristiques" method="get">
    <input type="hidden" name="nom" value="triercaracteristiques"/>
    ID de la caracteristique : <input type="text" name="caracteristique" size="4" value=""/>
    <input type="submit" name="valider" value="OK"/>
  </form>

</div>
