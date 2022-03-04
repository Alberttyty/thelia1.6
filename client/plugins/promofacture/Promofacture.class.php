<?php

include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");	
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
	
	class Promofacture extends PluginsClassiques{
		
		function Promofacture(){
			$this->PluginsClassiques();	
		}
    
    function init(){
		  $this->ajout_desc("Promofacture", "Afficher le prix de départ sur la facture", "", 1);
    }
  
		function apresVenteprod($venteprod,$pos){
      $produit=new Produit();
      $produit->charger($venteprod->ref);
      if($produit->promo==1){
        if($produit->prix!=0&&$venteprod->prixu) $pourcentage = round((100 * ($produit->prix - $venteprod->prixu)/$produit->prix),0);
  		  else $pourcentage=0;
        $venteprod->titre.=" (en promo : prix de départ $produit->prix € soit -$pourcentage%) ";
        $venteprod->maj();
      }
    }             
		
	}

?>
