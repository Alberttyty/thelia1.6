<?php

  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/nettoyage.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produitdesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubrique.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubriquedesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Image.class.php");
  include_once(realpath(dirname(__FILE__)) . "/Amazone.class.php");
  /*include_once(realpath(dirname(__FILE__)) . "/php-export-data.class.php");*/
  include_once(realpath(dirname(__FILE__)) . "/../../../client/plugins/declibre/Declibre.class.php");

	autorisation("amazone");
 	
	header("Content-Type: text/tab-separated-values");
	header("Content-disposition: filename=export_fichier_de_stock" . ".txt");

$rubrique = new Rubrique();	
$rubriquedesc = new Rubriquedesc();	
$produit = new Produit();
$produitdesc = new Produitdesc();
$declibre = new Declibre();   
$amazone = new Amazone();     
/*$amazoneproduits = new Amazoneproduits();*/   
$image = new Image();  

$entete='"TemplateType=Clothing"	"Version=2015.0310"	"Les 3 lignes supérieures sont réservées à Amazon.com. Ne pas modifier ou supprimer les 3 lignes supérieures."								"Informations sur l\'offre - Informations sur l\'offre : ces attributs sont requis pour que votre article puisse être acheté par les clients sur le site."																	"Dimensions - Dimensions du produit : ces attributs spécifient la taille et le poids d\'un produit."		"Découverte d\'article - Informations de découverte d\'article : ces attributs ont un effet sur la manière dont les clients peuvent trouver votre produit sur le site à l\'aide du navigateur ou de la fonction de recherche."											"Images - Informations d\'image : voir l\'onglet Instructions sur l\'image pour plus de détails."										"Expédition - Ces colonnes sont destinées à toute information concernant l’expédition de commandes traitées par Expédié par Amazon ou par le vendeur."							"Variation - Informations de variation : diffusez ces attributs si votre produit est disponible en différents modèles (par exemple, couleur ou puissance en watts)."				"Compliance - Conformité - Informations de conformité : attributs utilisés pour la conformité avec les lois en matière de consommation dans le pays ou la région où l\'article est vendu"					"Ces attributs créent des listes de produits bien fournies pour vos acheteurs."																																						
"SKU"	"ID du produit"	"Type d\'ID du produit"	"Titre"	"Marque"	"Type de vêtement"	"Description du produit"	"Mettre à jour Supprimer"	"Numéro de modèle"	"Numéro de pièce"	"Prix ​​standard"	"Devise"	"quantité"	"Délai de traitement"	"Prix réduit"	"Date de début de la remise"	"Date de fin de la remise"	"Nombre produits identiques en une fois"	"Quantité de lot"	"Nombre d\' articles"	"message cadeau en option"	"emballage cadeau en option"	"Le fabricant a arrêté la production du produit"	"registered-parameter"	"Date de sortie"	"Date de remise en stock"	"Régions d’expéditions du vendeur"	"Poids de l\'envoi"	"Unité de mesure du poids de l’expédition du site internet"	"Catégorie de produits (Code arborescence recommandé)"	"Termes de recherche1"	"Termes de recherche2"	"Termes de recherche3"	"Termes de recherche4"	"Termes de recherche5"	"Mots-clés platinum1"	"Mots-clés platinum2"	"Mots-clés platinum3"	"Mots-clés platinum4"	"Mots-clés platinum5"	"URL de l\'image principale"	"URL d\'une autre image1"	"URL d\'une autre image2"	"URL d\'une autre image3"	"URL d\'une autre image4"	"URL d\'une autre image5"	"URL d\'une autre image6"	"URL d\'une autre image7"	"URL d\'une autre image8"	"URL de l’image échantillon"	"ID du centre de distribution"	"longueur colis"	"largeur colis"	"hauteur colis"	"Unité de mesure de la longueur du paquet"	"poids colis"	"Unité de mesure du poids du paquet"	"Parenté"	"SKU Parent"	"Type de relation"	"Thème variation"	"mentions légales"	"Directive EU relative à la Sécurité des Jouets - Avertissement à propos de l’âge"	"Directive EU relative à la Sécurité des Jouets - Avertissement non relatif à l’âge"	"Directive EU relative à la Sécurité des Jouets - Avertissement relatif à la langue"	"Pays d\'origine"	"Nom du Modèle"	"forme"	"Département"	"Couleur fabricant"	"Nom de couleur standardisé"	"Collection (Saison + Année)"	"Instructions entretien"	"Taille standardisée (valeur valide)"	"Taille fournisseur"	"Composition matiere"	"Type matière extérieure"	"Style du haut d\'un maillot 2 pièces"	"Style du bas d\'un maillot 2 pièces"	"Opacite"	"Produit pour adulte"	"Type matière interieure"	"Nom style"	"Type fermeture"	"délavage Jeans"	"Type coupe"	"unité de mesure pour longueur de Jeans"	"Longueur entrejambe (Jeans)"	"unité de mesure pour largeur de Jeans"	"Tour de taille (Jeans)"	"Longueur ceinture"	"unité de mesure pour ceinture"	"occasion"	"Type manche"	"Longueur vêtement"	"Forme encolure"	"Bonnet soutien gorge"	"tour de dos"	"unité tour de dos"	"Caracteristiques particulieres"	"Dessin du tissu"	"Tour cou"	"unité de mesure tour du cou"	"Style col"	"Type d\'ajustement"
"item_sku"	"external_product_id"	"external_product_id_type"	"item_name"	"brand_name"	"product_subtype"	"product_description"	"update_delete"	"model"	"part_number"	"standard_price"	"currency"	"quantity"	"fulfillment_latency"	"sale_price"	"sale_from_date"	"sale_end_date"	"max_aggregate_ship_quantity"	"item_package_quantity"	"number_of_items"	"offering_can_be_gift_messaged"	"offering_can_be_giftwrapped"	"is_discontinued_by_manufacturer"	"missing_keyset_reason"	"product_site_launch_date"	"restock_date"	"merchant_shipping_group_name"	"website_shipping_weight"	"website_shipping_weight_unit_of_measure"	"recommended_browse_nodes"	"generic_keywords1"	"generic_keywords2"	"generic_keywords3"	"generic_keywords4"	"generic_keywords5"	"platinum_keywords1"	"platinum_keywords2"	"platinum_keywords3"	"platinum_keywords4"	"platinum_keywords5"	"main_image_url"	"other_image_url1"	"other_image_url2"	"other_image_url3"	"other_image_url4"	"other_image_url5"	"other_image_url6"	"other_image_url7"	"other_image_url8"	"swatch_image_url"	"fulfillment_center_id"	"package_length"	"package_width"	"package_height"	"package_length_unit_of_measure"	"package_weight"	"package_weight_unit_of_measure"	"parent_child"	"parent_sku"	"relationship_type"	"variation_theme"	"legal_disclaimer_description"	"eu_toys_safety_directive_age_warning"	"eu_toys_safety_directive_warning"	"eu_toys_safety_directive_language"	"country_of_origin"	"model_name"	"item_shape"	"department_name"	"color_name"	"color_map"	"collection_name"	"care_instructions"	"size_map"	"size_name"	"material_composition"	"outer_material_type"	"top_style"	"bottom_style"	"opacity"	"is_adult_product"	"inner_material_type"	"style_name"	"closure_type"	"fabric_wash"	"waist_style"	"inseam_length_unit_of_measure"	"inseam_length"	"waist_size_unit_of_measure"	"waist_size"	"belt_length_derived"	"belt_length_unit_of_measure"	"lifestyle"	"sleeve_type"	"item_length_description"	"neck_style"	"cup_size"	"band_size_num"	"band_size_num_unit_of_measure"	"special_features"	"pattern_type"	"neck_size"	"neck_size_unit_of_measure"	"collar_style"	"fit_type"
';
echo $entete;

/*$exporter = new ExportDataExcel('browser', 'export_fichier_de_stock.xls');
$exporter->initialize();
$exporter->addRow(array("sku","product-id","product-id-type","price","minimum-seller-allowed-price","maximum-seller-allowed-price","item-condition","quantity","add-delete","will-ship-internationally","expedited-shipping","item-note","fulfillment-center-id"));
*/
  
$productidtype = new Variable();
$productidtype->charger("amazone-product-id-type");
$itemcondition = new Variable();
$itemcondition->charger("amazone-item-condition");
$willshipinternationally = new Variable();
$willshipinternationally->charger("amazone-will-ship-internationally");
$expeditedshipping = new Variable();
$expeditedshipping->charger("amazone-expedited-shipping");
$brandname = new Variable();
$brandname->charger("amazone-brand-name");
$urlsite = new Variable();
$urlsite->charger("urlsite");
$collectionname = new Variable();
$collectionname->charger("amazone-collection-name");
$recommendedbrowsenodes = new Variable();
$recommendedbrowsenodes->charger("amazone-recommended-browse-nodes");

$query_prod = 'select * from '.$produit->table.' where 1';
$resul_prod = mysql_query($query_prod, $produit->link);
while($prod = mysql_fetch_object($resul_prod)){
  $query_decli = 'select * from '.$declibre->table.' where ref="'.$prod->ref.'"';
  $resul_decli = mysql_query($query_decli, $declibre->link);
  
  $produitdesc->charger($prod->id);
  
  $rubrique->charger($prod->rubrique);
  $parent=$rubrique->parent;
  while($parent!=0){
    $rubrique->charger($rubrique->parent);
    $parent=$rubrique->parent;
  }
  $rubriquedesc->charger($rubrique->id);
  
  $imgs=array("","","","","","","","");
  $query_image = "select * from $image->table where produit=\"$prod->id\" order by classement limit 0,8"; 
  $resul_image = mysql_query($query_image, $rubrique->link);
  $i=0;
  while($img=mysql_fetch_object($resul_image)){
    if($img->fichier!="")$imgs[$i]=$urlsite->valeur.'/client/gfx/photos/produit/'.$img->fichier;
    $i=$i+1;
  }
  
  $amazone->rubrique=$prod->rubrique;
  $amazone->categorie="";
  $amazone->charger_rubrique();
  $categorie=$amazone->categorie;
  
  /*$amazoneproduits->charger_reference($prod->ref);*/
  
  while($decli = mysql_fetch_object($resul_decli)){
    if($decli->ref!=""/*&&$amazoneproduits->recommended_browse_nodes!=0*/){
    
      $ligne='';
      //sku
      $ligne.='"'.$prod->ref."-".$decli->id.'"	';
      //external_product_id
      $ligne.='"'.$decli->lien.'"	';
      //external_product_id_type
      $ligne.='"'.$productidtype->valeur.'"	'; 
      //item_name
      $ligne.='"'.$amazone->cleanInput($produitdesc->titre).' - '.$amazone->cleanInput($decli->declinaison).'"	';
      //brand_name
      $ligne.='"'.$amazone->cleanInput($brandname->valeur).'"	';
      //product_subtype
      $ligne.='"'.$categorie.'"	';
      //product_description
      $ligne.='"'.$amazone->cleanInput($produitdesc->chapo).' '.$amazone->cleanInput($produitdesc->description).'"	';
      //update_delete
      $ligne.='"Update"	';
      //model
      $ligne.='""	';      
      //part_number
      $ligne.='""	';
      //standard_price
      if(($prod->promo)&&($decli->prix2!=0)) $ligne.='"'.$decli->prix2.'"	';
      elseif(($prod->promo)) $ligne.='"'.$prod->prix2.'"	';
      elseif($decli->prix!=0) $ligne.='"'.$decli->prix.'"	'; 
      else $ligne.='"'.$prod->prix.'"	';
      //currency
      $ligne.='"EUR"	';
      //quantity
      if($prod->ligne==1) $ligne.='"'.$decli->stock.'"	';
      else  $ligne.='"0"	'; 
      //fulfillment_latency
      $ligne.='""	'; 
      //sale_price
      $ligne.='""	'; 
      //sale_from_date
      $ligne.='""	'; 
      //sale_end_date
      $ligne.='""	'; 
      //max_aggregate_ship_quantity
      $ligne.='""	'; 
      //item_package_quantity
      $ligne.='""	';
      //number_of_items
      $ligne.='""	';
      //offering_can_be_gift_messaged
      $ligne.='""	';
      //offering_can_be_giftwrapped
      $ligne.='""	';
      //is_discontinued_by_manufacturer
      $ligne.='""	';
      //missing_keyset_reason
      $ligne.='""	';
      //product_site_launch_date
      $ligne.='""	';
      //restock_date
      $ligne.='""	';
      //merchant_shipping_group_name
      $ligne.='""	';
      //website_shipping_weight
      $poids=$prod->poids;
      if($poids==0) $poids=0.01;
      $ligne.='"'.$poids.'"	';
      //website_shipping_weight_unit_of_measure
      $ligne.='"KG"	';
      //recommended_browse_nodes
      $valeur=$amazone->getCarac('recommended_browse_nodes');
      if($valeur=="")$valeur=$recommendedbrowsenodes->valeur;
      $ligne.='"'.$amazone->cleanInput($valeur).'"	';
      //generic_keywords1
      $ligne.='""	';
      //generic_keywords2
      $ligne.='""	';
      //generic_keywords3
      $ligne.='""	';
      //generic_keywords4
      $ligne.='""	';
      //generic_keywords5
      $ligne.='""	';
      //platinum_keywords1
      $ligne.='""	';
      //platinum_keywords2
      $ligne.='""	';
      //platinum_keywords3
      $ligne.='""	';
      //platinum_keywords4
      $ligne.='""	';
      //platinum_keywords5
      $ligne.='""	';
      //main_image_url
      $ligne.='"'.$imgs[0].'"	';
      //other_image_url1
      $ligne.='"'.$imgs[1].'"	';
      //other_image_url2
      $ligne.='"'.$imgs[2].'"	';
      //other_image_url3
      $ligne.='"'.$imgs[3].'"	';
      //other_image_url4
      $ligne.='"'.$imgs[4].'"	';
      //other_image_url5
      $ligne.='"'.$imgs[5].'"	';
      //other_image_url6
      $ligne.='"'.$imgs[6].'"	';
      //other_image_url7
      $ligne.='"'.$imgs[7].'"	';
      //other_image_url8
      $ligne.='"'.$imgs[8].'"	';
      //swatch_image_url
      $ligne.='""	';
      //fulfillment_center_id
      $ligne.='""	';
      //package_length
      $ligne.='""	';
      //package_width
      $ligne.='""	';
      //package_height
      $ligne.='""	';
      //package_length_unit_of_measure
      $ligne.='""	';
      //package_weight
      $ligne.='""	';
      //package_weight_unit_of_measure
      $ligne.='""	';
      //parent_child
      $ligne.='""	';
      //parent_sku
      $ligne.='""	';
      //relationship_type
      $ligne.='""	';
      //variation_theme
      $ligne.='""	';
      //legal_disclaimer_description
      $ligne.='""	';
      //eu_toys_safety_directive_age_warning
      $ligne.='""	';
      //eu_toys_safety_directive_warning
      $ligne.='""	';
      //eu_toys_safety_directive_language
      $ligne.='""	';
      //country_of_origin
      $ligne.='""	';
      //model_name
      $ligne.='"'.$amazone->cleanInput($produitdesc->titre).'"	';
      //item_shape
      $ligne.='""	';
      //department_name
      $ligne.='"'.$amazone->cleanInput($amazone->getCarac('department_name')).'"	';
      //color_name
      $ligne.='"'.$amazone->cleanInput($amazone->getCarac('color_name')).'"	';
      //color_map
      $ligne.='"'.$amazone->cleanInput($amazone->getCarac('color_name')).'"	';
      //collection_name
      $valeur=$amazone->getCarac('collection_name');
      if($valeur=="")$valeur=$collectionname->valeur;
      $ligne.='"'.$amazone->cleanInput($valeur).'"	';
      //care_instructions                    
      $ligne.='""	';
      //size_map
      $ligne.='"Taille unique"	';
      //size_name
      $ligne.='"Taille unique"	';
      //material_composition
      $ligne.='"'.$amazone->cleanInput($amazone->getCarac('material_composition')).'"	';
      //outer_material_type
      $ligne.='"'.$amazone->cleanInput($amazone->getCarac('material_composition')).'"	';
      //top_style
      $ligne.='""	';
      //bottom_style
      $ligne.='""	';
      //opacity
      $ligne.='""	';
      //is_adult_product
      $ligne.='"false"	';
      //inner_material_type
      $ligne.='""	';
      //style_name
      $ligne.='""	';
      //closure_type
      $ligne.='""	';
      //fabric_wash
      $ligne.='""	';
      //waist_style
      $ligne.='""	';
      //inseam_length_unit_of_measure
      $ligne.='""	';
      //inseam_length
      $ligne.='""	';
      //waist_size_unit_of_measure
      $ligne.='""	';
      //waist_size
      $ligne.='""	';
      //belt_length_derived
      $ligne.='""	';
      //belt_length_unit_of_measure
      $ligne.='""	';
      //lifestyle
      $ligne.='""	';
      //sleeve_type
      $ligne.='""	';
      //item_length_description
      $ligne.='""	';
      //neck_style
      $ligne.='""	';
      //cup_size
      $ligne.='""	';
      //band_size_num
      $ligne.='""	';
      //band_size_num_unit_of_measure
      $ligne.='""	';
      //special_features
      $ligne.='""	';
      //pattern_type
      $ligne.='""	';
      //neck_size
      $ligne.='""	';
      //neck_size_unit_of_measure
      $ligne.='""	';
      //collar_style
      $ligne.='""	';
      //fit_type
      $ligne.='""	';
      $ligne.='
';
      echo supprAccent($ligne);
    }
    
  }
  
}

/*$exporter->finalize();*/
exit();

?>
