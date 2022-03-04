<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("ebay");

	include_once(realpath(dirname(__FILE__)) . "/Ebay.class.php");
  include_once(realpath(dirname(__FILE__)) . "/classes/Ebayproduits.class.php");
    
  $compteur=0;
  $effacer=0;
  
  if($_REQUEST['action_ebay'] == "importer_ebay" && is_readable($_FILES['fichiercsv']['tmp_name'])){
           
    $fichier = fopen($_FILES['fichiercsv']['tmp_name'], "r");
    
    $premiere_ligne=true;
    $key_ItemID=0;
    $key_CustomLabel=0;
    
    $ebay= new Ebay();
		
		while (($data = fgetcsv($fichier, 0, ",")) !== FALSE){
             
      if($premiere_ligne){
        foreach($data as $k => $v){
          if($v=="ItemID") {
            $key_ItemID=$k;
          }
          if($v=="CustomLabel") {
            $key_CustomLabel=$k;
          }
        }
        $premiere_ligne=false;
      }
      elseif($key_ItemID!=0&&$key_CustomLabel!=0){  
        $ebay->ajouterItemID($data[$key_CustomLabel],$data[$key_ItemID]);
        $compteur=$compteur+1; 
      }
    
    }

  }
  
  if($_REQUEST['action_ebay'] == "effacer_ebay"){
  
    $ebayproduits= new Ebayproduits();
    $ebayproduits->purge();
    $effacer=1;
  
  }
  
?>

<div id="contenu_int">

  <p>
  
    <a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=ebay" class="lien04">Ebay</a>
    
  </p>
  
  <div id="bloc_description">
    <div class="entete_liste_config">
    	<div class="titre">EXPORTER LES PRODUIT</div>
    </div>
    <div class="bordure_bottom">
      <ul class="ligne_claire_rub">
      	<li><a href="../client/plugins/ebay/export_produits.php">Exporter</a></li>
      </ul>
    </div>
  </div>
  
  <div id="bloc_description" style="margin-top:30px;">                                     
    <div class="entete_liste_config" id="import">
    	<div class="titre">IMPORTER LES IDENTIFIANTS EBAY</div>
    </div>
    <div class="bordure_bottom">
      <form action="module.php?nom=ebay#import" method="post" enctype="multipart/form-data" >
  			<input type="hidden" name="action_ebay" value="importer_ebay" />
  			<ul class="ligne_claire_rub">
        	<li>
            Importer le fichier fourni par Ebay
          </li>
        </ul>
        <ul class="ligne_claire_rub">
          <li style="<?php if($compteur>0) echo "display:none;"; ?>">
    				<input type="file" name="fichiercsv" />
    				<input type="submit" value="OK" />
          </li>
          <?php if($compteur>0) echo "<li>".$compteur." produit(s) traité(s)</li>"; ?>
  		</ul>
  	</form>
    </div>
  </div>
  
  <div id="bloc_description" style="margin-top:30px;">                                     
    <div class="entete_liste_config" id="delete">
    	<div class="titre">VIDER LES IDENTIFIANTS EBAY ENREGISTREE</div>
    </div>
    <div class="bordure_bottom">
      <form action="module.php?nom=ebay#delete" method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer les identifiants enregistrés dans la base de données ?\r\n(il faudra aussi supprimer vos produits sur ebay)');">
  			<input type="hidden" name="action_ebay" value="effacer_ebay" />
        <ul class="ligne_claire_rub">
          <li style="<?php if($effacer>0) echo "display:none;"; ?>">
    				<input type="submit" value="EFFACER" />
          </li>
          <?php if($effacer>0) echo "<li>Identifiants effacés</li>"; ?>
  		</ul>
  	</form>
    </div>
  </div>

</div>