<?php
  include_once(realpath(dirname(__FILE__)) . "/Nettoyagephoto.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Image.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/nettoyage.php");
	autorisation("nettoyagephoto");
?>
<div id="contenu_int"> 
      <p align="left"><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" />Nettoyage photo             
    </p>
<div class="entete_liste_client">
	<div class="titre">Nettoyage photo</div>
</div>
<?php

if($_GET['valider']!=""){
  $nettoyagephoto = new Nettoyagephoto();       
  $image = new Image();
  $type="produit";
  if($_GET['valider']=="Nettoyer Rubrique"){
    $type="rubrique";
  }
  if($_GET['valider']=="Nettoyer Contenu"){
    $type="contenu";
  }
  if($_GET['valider']=="Nettoyer Dossier"){
    $type="dossier";
  }
  $liste=$nettoyagephoto->query_liste('SELECT id,fichier FROM '.$image->table.' WHERE '.$type.'!=0');
  foreach($liste as $key => $value){
    
    $dot = strrpos($value->fichier, '.');
    if ($dot !== false) {
      $fich = eregfic(substr($value->fichier, 0, $dot));
			$extension = strtolower(substr($value->fichier, $dot+1));
      $nouveaunom=$fich.".".$extension;
      
      if($nouveaunom!=""){
        $dossier=realpath(dirname(__FILE__))."/../../../client/gfx/photos/".$type."/";
        //echo  $dossier.$value->fichier,$dossier.$nouveaunom."<br>";
        if(rename($dossier.$value->fichier,$dossier.$nouveaunom)){
        
          $image = new Image();
          $image->charger($value->id);
          $image->fichier=$nouveaunom;
          $image->maj();
        
        }
      }
      
    }
    
  }
}

?>
<form action="module.php" name="form_bdd" id="form_bdd" method="get" enctype="multipart/form-data">
<input type="hidden" name="nom" value="nettoyagephoto">
<input type="submit" name="valider" value="Nettoyer Produit">
</form><br/>
<form action="module.php" name="form_bdd" id="form_bdd" method="get" enctype="multipart/form-data">
<input type="hidden" name="nom" value="nettoyagephoto">
<input type="submit" name="valider" value="Nettoyer Rubrique">
</form><br/>
<form action="module.php" name="form_bdd" id="form_bdd" method="get" enctype="multipart/form-data">
<input type="hidden" name="nom" value="nettoyagephoto">
<input type="submit" name="valider" value="Nettoyer Contenu">
</form><br/>
<form action="module.php" name="form_bdd" id="form_bdd" method="get" enctype="multipart/form-data">
<input type="hidden" name="nom" value="nettoyagephoto">
<input type="submit" name="valider" value="Nettoyer Dossier">
</form>

</div>
