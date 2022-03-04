<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdisp.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdispdesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracteristique.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracteristiquedesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracval.class.php");
	autorisation("import");
?>
<div id="contenu_int"> 
      <p align="left"><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" />Importation             
    </p>
<div class="entete_liste_client">
	<div class="titre">Importation</div>
</div>
<?php

include_once(realpath(dirname(__FILE__)) . "/Import.class.php");
$import = new Import();

/*
//CORRECTIONS DE TITRES
$produit = new Produit();
$liste=$produit->query_liste('SELECT id FROM '.$produit->table.' WHERE 1');
  foreach($liste as $key => $value){
    
    $produitdesc = new Produitdesc();
    $produitdesc->charger($value->id);
    $produitdesc->titre=str_replace("75cl","",$produitdesc->titre);
    $produitdesc->titre=str_replace("70cl","",$produitdesc->titre);
    $produitdesc->titre=str_replace("100cl","",$produitdesc->titre);
    $produitdesc->titre=str_replace("150cl","",$produitdesc->titre);
    $produitdesc->titre=str_replace("35cl","",$produitdesc->titre);
    $produitdesc->titre=trim($produitdesc->titre);
    $produitdesc->maj();
    
  }
*/

function traiter_ligne($data){

  //PRODUIT
  $produit = new Produit();
  if($produit->charger($data[0]))
  {
    $maj=true;
  }
  else{
    $maj=false;
  }
  $produit->ref = $data[0];
  $produit->datemodif = date("Y-m-d H:i:s");
  $produit->prix = str_replace(",", ".", $data[10]);
  $produit->rubrique = $data[1];
  $produit->poids = 0;
  $produit->stock = 1000;
  $produit->ligne = 1;
  $produit->tva = '0.2';  
  if($maj){
    $produit->maj();
  }
  else{
    $lastid = $produit->add();
    $produit->id = $lastid;
  }
  
  //DESCRIPTION
  $produitdesc = new Produitdesc();
  if($produitdesc->charger($produit->id,1))
  {
    $maj=true;
  }
  else{
    $maj=false;
  }
  $produitdesc->chapo=$data[5];
  $produitdesc->description = "<p>".$data[6]."</p>";
  $produitdesc->postscriptum = "";
  $produitdesc->produit = $produit->id;
  $produitdesc->lang = 1;
  $produitdesc->titre = $data[2];
  if($maj){
    $produitdesc->maj();
  }
  else{
    $produitdesc->add();
    $produitdesc->reecrire();
  }
  
  //CARACTERISTIQUES
  $produit->delete_cascade('Caracval', 'produit', $produit->id);
  //CONTENU
  traiter_caracteristique($produit->id,$data[3],6);
  //MILLESIME
  traiter_caracteristique($produit->id,$data[4],1);
  //ACCORD
  traiter_caracteristique($produit->id,$data[7],3);
  //TEMPERATURE
  traiter_caracteristique($produit->id,$data[8],4);
  //GARDE
  traiter_caracteristique($produit->id,$data[9],5);
  
}

function traiter_caracteristique($produit_id,$valeur,$caracteristique){
  
  if($valeur!=""){
  
    $caracdispdesc = new Caracdispdesc(); 
    $caracdisp = new Caracdisp();
    $caracdispdesc->getVars("
      select $caracdispdesc->table.id,$caracdispdesc->table.caracdisp,$caracdispdesc->table.lang,$caracdispdesc->table.titre,$caracdispdesc->table.classement
      from $caracdispdesc->table,$caracdisp->table
      where $caracdispdesc->table.titre=\"".$valeur."\"
      and $caracdispdesc->table.lang=1 
      and $caracdisp->table.caracteristique=$caracteristique 
      and $caracdispdesc->table.caracdisp=$caracdisp->table.id
    ");
    
    if($caracdispdesc->caracdisp!=0||$caracdispdesc->caracdisp!="")
    {
      $caracval = new Caracval();
      $caracval->produit=$produit_id;
      $caracval->caracteristique=$caracteristique;
      $caracval->caracdisp=$caracdispdesc->caracdisp;
      $caracval->add();
    }
    else{
      $caracval = new Caracval();
      $caracval->produit=$produit_id;
      $caracval->caracteristique=$caracteristique;
      $caracval->valeur=$valeur;
      $caracval->add();
    }
  
  }

}

$tmp_file = $_FILES['fichiercsv']['tmp_name'];
if(!empty($_FILES['fichiercsv']['tmp_name'])){
  
  $content_dir = '../client/plugins/import/tmp/';
  
  if(!is_uploaded_file($tmp_file))
  {
  	exit("Le fichier est introuvable.");
  }
  
  $name_file = $_FILES['fichiercsv']['name'];	
  
  if(!move_uploaded_file($tmp_file, $content_dir . $name_file)){
		exit("Impossible de copier le fichier dans $content_dir");
	}
  
  if(file_exists("$content_dir"."$name_file")) {	
		//on lis la 1ere ligne pour vÚrifier et gÚnÚrer la liste des champs
		$fp=fopen("$content_dir"."$name_file", 'r');
		
    if ($fp)
    {
    $nb=0;
    	/*Tant que l'on est pas Ó la fin du fichier*/
    	while (($data = fgetcsv($fp, 1000, ";")) !== FALSE)
    	{
        traiter_ligne($data);
        $nb=$nb+1;
    	}
    	/*On ferme le fichier*/
    	fclose($fp);
    }
    
    echo $nb." lignes importées.";
    
  }
  
  
}

?>
<form action="" name="form_bdd" id="form_bdd" method="post" enctype="multipart/form-data">
<input type="file" name="fichiercsv" size="16">
<input type="submit" value="OK">
</form>

</div>
