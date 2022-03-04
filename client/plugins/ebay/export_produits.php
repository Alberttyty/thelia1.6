<?php

	//include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/nettoyage.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produitdesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Image.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
  include_once(realpath(dirname(__FILE__)) . "/Ebay.class.php");
  /*include_once(realpath(dirname(__FILE__)) . "/php-export-data.class.php");*/
  include_once(realpath(dirname(__FILE__)) . "/../../../client/plugins/declibre/Declibre.class.php");

	autorisation("ebay");
  
  mb_internal_encoding("UTF-8");
 	
	header("Content-Type: application/csv-tab-delimited-table; charset=utf-8");
	header("Content-disposition: filename=ebay_fichier_de_stock" . ".csv");
	
$produit = new Produit();
$produitdesc = new Produitdesc();
$declibre = new Declibre();   
$image = new Image();
$ebay = new Ebay();   
$ebayproduits=new Ebayproduits();

$entete='*Action(SiteID=France|Country=FR|Currency=EUR|Version=745|CC=UTF-8),*Category,*Title,Subtitle,*Description,*ConditionID,PicURL,*Quantity,*Format,*StartPrice,BuyItNowPrice,*Duration,ImmediatePayRequired,*Location,GalleryType,PayPalAccepted,PayPalEmailAddress,PaymentInstructions,DomesticInsuranceOption,DomesticInsuranceFee,InternationalInsuranceOption,InternationalInsuranceFee,StoreCategory,ShippingDiscountProfileID,DispatchTimeMax,ShippingType,ShippingService-1:Option,ShippingService-1:Cost,ShippingService-1:Priority,ShippingService-1:FreeShipping,IntlShippingService-1:Locations,IntlShippingService-1:Option,IntlShippingService-1:Cost,IntlShippingService-1:Priority,ShippingService-2:Option,ShippingService-2:Cost,ShippingService-2:Priority,CustomLabel,ReturnsAcceptedOption,ReturnsWithinOption,ShippingCostPaidByOption,AdditionalDetails,ShippingProfileName,ReturnProfileName,PaymentProfileName,PackagingHandlingCosts,Product:EAN,ItemID,C:Marque
';
echo $entete;
  
$urlsite = new Variable();
$urlsite->charger("urlsite");
$paypal2 = new Variable();
$paypal2->charger("paypal2");
$conditionid = new Variable();
$conditionid->charger("ebay-conditionid");
$location = new Variable();
$location->charger("ebay-location");
$marque = new Variable();
$marque->charger("ebay-marque");
$shippingservice1 = new Variable();
$shippingservice1->charger("ebay-shippingservice1");
$shippingservice1_cost = new Variable();
$shippingservice1_cost->charger("ebay-shippingservice1_cost");
$intlshippingservice1 = new Variable();
$intlshippingservice1->charger("ebay-intlshippingservice1");
$intlshippingservice1_cost = new Variable();
$intlshippingservice1_cost->charger("ebay-intlshippingservice1_cost");


$query_prod = 'select * from '.$produit->table.' where ligne=1';
$resul_prod = mysql_query($query_prod,$produit->link);
while($prod = mysql_fetch_object($resul_prod)){

  $produitdesc->charger($prod->id);
  $query_image = "select * from $image->table where produit=\"$prod->id\" order by classement limit 0,1"; 
  $resul_image = mysql_query($query_image, $produit->link);
  $img = mysql_fetch_object($resul_image);
  
  $query_decli = 'select * from '.$declibre->table.' where ref="'.$prod->ref.'"';
  $resul_decli = mysql_query($query_decli, $declibre->link);
  
  $ebay->rubrique=$prod->rubrique;
  $ebay->categorie="";
  $ebay->charger_rubrique();
  $categorie=$ebay->categorie;
  
  while($decli = mysql_fetch_object($resul_decli)){
    $reference=$decli->lien;
    if($reference!=""&&$categorie!=""/*&&$decli->stock>0*/){
    
      if($ebayproduits->charger_reference($reference)){
        $action='Revise,';
        $itemid=$ebayproduits->itemid.',';
      }
      else {
        $action='Add,';
        $itemid='0,';
      }
    
      $ligne='';  
      //*Action
      $ligne.=$action;
      //*Category
      $ligne.=$categorie.',';
      //*Title
      $ligne.='"'.$ebay->cleanInput($produitdesc->titre).' - '.$ebay->cleanInput($decli->declinaison).'",';
      //*Subtitle
      $ligne.='"'.$ebay->cleanInput($produitdesc->chapo).'",';
      //*Description
      $ligne.='"'.$ebay->cleanInput($produitdesc->description).'",';   
      //*Condition
      $ligne.=$conditionid->valeur.',';
      //*PicURL
      //$ligne.=''.$urlsite->valeur.'/'.FICHIER_URL.'/client/gfx/photos/produit/'.$img->fichier.',';
      $ligne.=''.$urlsite->valeur.'/client/gfx/photos/produit/'.$img->fichier.',';
      //*Quantity
      $ligne.=''.$decli->stock.','; 
      //*Format
      $ligne.='FixedPrice,'; 
      //*StartPrice
      if(($prod->promo)&&($decli->prix2!=0)) $ligne.=number_format($decli->prix2,2,'.','').',';
      elseif(($prod->promo)) $ligne.=number_format($prod->prix2,2,'.','').',';
      elseif($decli->prix!=0) $ligne.=number_format($decli->prix,2,'.','').','; 
      else $ligne.=number_format($prod->prix,2,'.','').',';
      //BuyItNowPrice
      $ligne.='0.00,';
      //*Duration
      $ligne.='GTC,'; 
      //ImmediatePayRequired                      
      $ligne.='0,';
      //*Location
      $ligne.='"'.$location->valeur.'",';
      //GalleryType
      $ligne.=',';
      //PayPalAccepted
      $ligne.='1,';
      //PayPalEmailAddress
      $ligne.=''.$paypal2->valeur.',';
      //7 suivants
      for ($i=0;$i<7;$i++){    
        $ligne.=',';
      }
      //DispatchTimeMax
      $ligne.='0,';
      //ShippingType
      $ligne.='Flat,';
      //type de livraison
      $ligne.=$shippingservice1->valeur.',';
      //prix de la livraison
      $ligne.=$shippingservice1_cost->valeur.',';
      //priorite de la livraison
      $ligne.='1,';
      //livraison gratuite
      $ligne.='1,';
      //international
      $ligne.='Europe,';
      //type de livraison
      $ligne.=$intlshippingservice1->valeur.',';
      //prix de la livraison                              
      $ligne.=$intlshippingservice1_cost->valeur.',';
      //priorite de la livraison
      $ligne.='1,';
      //5 suivants
      for ($i=0;$i<3;$i++){
        $ligne.=',';
      } 
      //CustomLabel = code EAN
      $ligne.=$ebay->cleanInput($reference).',';
      $ligne.='ReturnsAccepted,';
      $ligne.='Days_14,';
      $ligne.='Buyer,';
      //4 suivants
      for ($i=0;$i<4;$i++){
        $ligne.=',';
      }
      //frais d'emballage
      $ligne.='0.00,';
      //reference de la declinaison = code EAN
      $ligne.=$ebay->cleanInput($reference).',';
      //item id
      $ligne.=$itemid;
      //marque
      $ligne.=$marque->valeur.',';
      $ligne.='
';
      echo supprAccent($ligne);
    }
    
  }
  
}

/*$exporter->finalize();*/
exit();

?>
