<?php
  include_once(realpath(dirname(__FILE__)) . "/../../../gestion/pre.php");
	include_once(realpath(dirname(__FILE__)) . "/Declibre.class.php");
  include_once(realpath(dirname(__FILE__)) . "/classes/Declibredesc.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

  $lang=$_GET['lang'];
	
	if($_GET['action'] == "declibresupprimer"){
		$declibre = new Declibre();
		$declibre->charger_id($_GET['id']);
		$declibre->delete();
	}
	
	elseif($_GET['action'] == "declibresupprimertout"){
		$declibre = new Declibre();
		$declibre->ref=$_GET['ref'];
		if($_GET['ref']!="") $declibre->delete_ref($_GET['ref']);
	}
	
	else if($_GET['action'] == "declibreajouter"){
		$declibre = new Declibre();
		$declibre->ref = $_GET['ref'];
		$declibre->add();
	}
	
	else if($_GET['action'] == "declibrecombinaisons"){
	   
	  $combinaisons_1=explode(",",$_GET['combinaison_1']);
	  $combinaisons_2=explode(",",$_GET['combinaison_2']);
	  $combinaisons=array();
	  
	  foreach ($combinaisons_1 as $key1 => $value1){
    
      foreach ($combinaisons_2 as $key2 => $value2){
      $combinaison=trim($value1)." | ".trim($value2);
      array_push($combinaisons,$combinaison);
      }

    }
    
    foreach ($combinaisons as $key => $value){
    
    $espaces=array("  ", "   ", "    ", "     ", "      ");
		$value=str_replace($espaces, " ", $value);
    
    $declibre = new Declibre();
		/*$declibre->charger_declinaison($value,$_GET['ref']);
		if($declibre->declinaison==""){*/
    
      $declibre->ref=$_GET['ref'];
      $declibre->id=$declibre->add();
      $declibredesc = new Declibredesc();
  		$declibredesc->declinaison=$value;
      $declibredesc->lang=$lang;
      $declibredesc->declibre=$declibre->id;
      $declibredesc->add();
   /* } */
    
    }
		
	}
		
	$query_declibre = "select * from $declibre->table where ref=\"". $declibre->ref . "\"";
	$resul_declibre = mysql_query($query_declibre, $declibre->link);
  
?>
  <ul class="ligne1">
				<li class="cellule" style="width:240px;">Déclinaison</li>
				<li class="cellule" style="width:65px;">Stock</li>
				<li class="cellule" style="width:65px;">Prix</li>
				<li class="cellule" style="width:65px;">Prix Promo</li>
        <li class="cellule" style="width:65px;">Lien</li>
	 </ul>

  
    <?php 	
    	while($row_declibre = mysql_fetch_object($resul_declibre)){
       $declibredesc = new Declibredesc();
      $declibredesc->charger($row_declibre->id,$lang);
      if($declibredesc->declinaison=="")$declibredesc->charger($row_declibre->id,1);
    ?>
    
    <ul class="lignesimple">
				<li class="cellule" style="width:240px; padding: 5px 0 0 5px;"><input type="text" name="declibretitre_<?php echo $row_declibre->id; ?>" value="<?php echo $declibredesc->declinaison; ?>" size="33" class="form" /></li>
				<li class="cellule_prix" style="padding: 5px 0 0 5px;"><input name="declibrestock_<?php echo $row_declibre->id; ?>" type="text" value="<?php echo $row_declibre->stock; ?>" size="4" class="form"/></li>
				<li class="cellule_prix" style="padding: 5px 0 0 5px;"><input type="text" name="declibreprix_<?php echo $row_declibre->id; ?>"  value="<?php echo $row_declibre->prix; ?>" size="4" class="form" /> &euro;</li>
        <li class="cellule_prix"  style="padding: 5px 0 0 5px;"><input type="text" name="declibreprix2_<?php echo $row_declibre->id; ?>" value="<?php echo $row_declibre->prix2; ?>" size="4" class="form" /> &euro;</li> 
				<li class="cellule_lien"  style="padding: 5px 0 0 5px;"><input type="text" name="declibrelien_<?php echo $row_declibre->id; ?>" value="<?php echo $row_declibre->lien; ?>" size="10" class="form" /></li>
		    <li class="cellule_suppr"  style="padding: 5px 0 0 5px;"><a href="#" onclick="declibre_supprimer(<?php echo $row_declibre->id; ?>);return false;" class="lien05">x</a></li>
    </ul>
	
   <?php
   	}
   ?>
   <ul class="lignesimple" style="padding-bottom:32px;">
   <li class="cellule" style="width:395px;"><a href="#" onclick="declibre_ajouter('<?php echo $declibre->ref; ?>');return false;" class="txt_vert_11">AJOUTER UNE LIGNE</a></li>
   <li class="cellule"  style="float:right;width:100px;text-align:right;padding: 5px 5px 0 5px;"><a href="#" onclick="declibre_supprimertout('<?php echo $declibre->ref; ?>');return false;" class="lien05">tout supprimer</a></li>
   </ul>
   <ul class="lignesimple">
   <li class="cellule" style="width:160px;">Valeurs 1</li>
   <li class="cellule" style="width:160px;">Valeurs 2</li>
   <li class="cellule" style="width:220px;"></li>
   </ul>
   <ul class="lignesimple">
   <li class="cellule" style="width:160px;"><input type="text" id="declibrecombinaison_1" name="declibrecombinaison_1" size="20" class="form" /></li>
   <li class="cellule" style="width:160px;"><input type="text" id="declibrecombinaison_2" name="declibrecombinaison_2" size="20" class="form" /></li>
   <li class="cellule" style="width:220px;"><a href="#" onclick="declibre_combinaisons('<?php echo $declibre->ref; ?>');return false;" class="txt_vert_11">CREER LES COMBINAISONS</a></li>
   </ul>	
   <ul class="lignesimple">
   <li class="cellule" style="width:500px;">Saisir des listes de valeurs s&eacute;par&eacute;es par une virgule.</li>
   </ul>
    <ul class="lignesimple">
   <li class="cellule" style="width:500px;">Exemple : Valeurs 1 = Noir, Vert, Rouge et Valeurs 2 = S, M, L, XL</li>
   </ul>
