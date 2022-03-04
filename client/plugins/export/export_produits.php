<?php

	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
	autorisation("export");
 	
	header("Content-type: text/csv");
	header("Content-disposition: filename=export_fichier_de_stock" . ".csv");
  
  $produit = new Produit(); 
  
  $entete='"Rubrique";"Ref";"Titre";"Description";"Declinaison";"Prix HT";"TVA";"Photo 1";"Photo 2";"Photo 3";"Photo 4";"Photo 5"
';
  echo $entete;
  
  $query_prod = "SELECT rubriquedesc.titre as 'rubrique',produit.id as 'produit_id',produit.ref,produitdesc.titre,produitdesc.description,declibre.declinaison,revendeur.prixrevendeur as 'prix',produit.tva
  FROM `produit`                                                                                                                                                                                                  
  LEFT JOIN `produitdesc` ON produit.id=produitdesc.produit
  LEFT JOIN `prixachat` ON produit.id=prixachat.produit
  LEFT JOIN `revendeur` ON produit.id=revendeur.produit
  LEFT JOIN `rubriquedesc` ON produit.rubrique=rubriquedesc.rubrique
  LEFT JOIN `declibre` ON produit.ref=declibre.ref
  WHERE 1 AND rubriquedesc.lang=1 AND produitdesc.lang=1
  ORDER BY produit.rubrique,produitdesc.titre,declibre.declinaison";
  $resul_prod = mysql_query($query_prod, $produit->link);
  while($prod = mysql_fetch_object($resul_prod)){
  
  $imgs=array("","","","","");
  $query_image = "select * from image where produit=\"$prod->produit_id\" order by classement limit 0,5"; 
  $resul_image = mysql_query($query_image, $produit->link);
  $i=0;
  while($img=mysql_fetch_object($resul_image)){
    if($img->fichier!="")$imgs[$i]=$urlsite->valeur.'/client/gfx/photos/produit/'.$img->fichier;
    $i=$i+1;
  }
    
      $ligne='';
      $ligne.='"'.$prod->rubrique.'";';
      $ligne.='"'.$prod->ref.'";';
      $ligne.='"'.$prod->titre.'";';
      $ligne.='"'.str_replace("\"","'",preg_replace("/(\r\n|\n|\r)/","",strip_tags(html_entity_decode($prod->description,ENT_COMPAT|ENT_HTML401,'UTF-8')))).'";';
      $ligne.='"'.$prod->declinaison.'";';
      $ligne.='"'.$prod->prix.'";';
      $ligne.='"'.$prod->tva.'";';
      //main_image_url
      $ligne.='"'.$imgs[0].'";';
      //other_image_url1
      $ligne.='"'.$imgs[1].'";';
      //other_image_url2
      $ligne.='"'.$imgs[2].'";';
      //other_image_url3
      $ligne.='"'.$imgs[3].'";';
      //other_image_url4
      $ligne.='"'.$imgs[4].'";';
      //other_image_url5
      $ligne.='"'.$imgs[5].'";';
      $ligne.='
';
      echo $ligne;
  
  }
  
  exit();

?>
