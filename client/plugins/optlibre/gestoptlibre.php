<?php
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
	include_once(realpath(dirname(__FILE__)) . "/Optlibre.class.php");
  include_once(realpath(dirname(__FILE__)) . "/classes/Optlibredesc.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");	
?>

<?php

  $lang=$_GET['lang'];
  
	if($_GET['action'] == "optlibremodifier"){
		
		$optlibre = new Optlibre();
		$optlibre->charger_id($_GET['id']);
    
    $optlibredesc = new Optlibredesc();
    $optlibredesc->charger($_GET['id'],$lang);
    $optlibredesc->lang=$lang;

		if(isset($_GET['option']))
			$optlibredesc->option = $_GET['option'];
			
	 if(isset($_GET['titre']))
			$optlibredesc->titre = $_GET['titre'];

		/*if($_GET['stock'] != "")
			$optlibre->stock = $_GET['stock'];*/

		if(isset($_GET['prix']))
			$optlibre->prix = $_GET['prix'];
		
		if(isset($_GET['prix2']))
			$optlibre->prix2 = $_GET['prix2'];
			
			$espaces=array("  ", "   ", "    ", "     ", "      ");
			$optlibredesc->option=str_replace($espaces, " ", $optlibredesc->option);
			
		$optlibre->maj();   
    if($optlibredesc->optlibre==""){
      $optlibredesc->optlibre=$optlibre->id;
      if($optlibredesc->option==""){
        $optlibredesc_tmp = new Optlibredesc();
        $optlibredesc_tmp->charger($_GET['id'],1);
        $optlibredesc->option=$optlibredesc_tmp->option;
      }
      if($optlibredesc->titre==""){
        $optlibredesc_tmp = new Optlibredesc();
        $optlibredesc_tmp->charger($_GET['id'],1);
        $optlibredesc->titre=$optlibredesc_tmp->titre;
      }
      $optlibredesc->add();
    }
    else {$optlibredesc->maj();}
	}
	
	else {
	
	
	if($_GET['action'] == "optlibresupprimer"){
		$optlibre = new Optlibre();
		$optlibre->charger_id($_GET['id']);
    $optlibredesc =  new Optlibredesc();
		$optlibredesc->delete("delete from $optlibredesc->table where optlibre=\"$optlibre->id\"");
    $optlibre->delete();
	}
	
	elseif($_GET['action'] == "optlibresupprimertout"){
		$optlibre = new Optlibre();
		$optlibre->ref=$_GET['ref'];
		if($_GET['ref']!="") $optlibre->delete_ref($_GET['ref']);
	}
	
	else if($_GET['action'] == "optlibreajouter"){
		$optlibre = new Optlibre();
		$optlibre->ref = $_GET['ref'];
		$optlibre->add();
    $optlibredesc = new Optlibredesc();
    $optlibredesc->optlibre=$optlibre->id;
    $optlibredesc->lang=$lang;
    $optlibredesc->add();
	}
	
	else if($_GET['action'] == "optlibrecombinaisons"){
	   /*
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
    
    $optlibre = new Optlibre();
		$optlibre->charger_option($value,$_GET['ref']);
		if($optlibre->option==""){
  		$optlibre->option=$value;
      $optlibre->ref=$_GET['ref'];
      $optlibre->add();
    }
    
    }*/
		
	}
	$optlibredesc =  new Optlibredesc();	
	$query_optlibre = "select $optlibre->table.id,$optlibre->table.ref,$optlibre->table.prix,$optlibre->table.prix2 from $optlibre->table where ref=\"". $optlibre->ref . "\"";
	$resul_optlibre = mysql_query($query_optlibre, $optlibre->link);
?>



  <ul class="ligne1">
				<li class="cellule" style="width:320px;">Option</li>
				<li class="cellule" style="width:80px;">Prix</li>
				<li class="cellule" style="width:80px;">Prix Promo</li>
	 </ul>

  
    <?php
    	while($row_optlibre = mysql_fetch_object($resul_optlibre)){
      $optlibredesc = new Optlibredesc();
      $optlibredesc->charger($row_optlibre->id,$lang);
      if($optlibredesc->titre==""&&$optlibredesc->option=="")$optlibredesc->charger($row_optlibre->id,1);
    ?>
    
    <ul class="lignesimple" style="height:96px;">
				<li class="cellule" style="width:320px; padding: 5px 0 0 5px;"><input type="text" name="titre_<?php echo $row_optlibre->id; ?>" onblur="$.ajax({type:'GET', url:'../client/plugins/optlibre/gestoptlibre.php', data:'action=optlibremodifier&lang=<?php echo $lang; ?>&id=<?php echo $row_optlibre->id; ?>' + '&titre=' + this.value });" value="<?php echo $optlibredesc->titre; ?>" size="33" class="form" /></li>
				<li class="cellule_prix" style="padding: 5px 0 0 5px;"><input type="text" name="optlibreprix_<?php echo $row_optlibre->id; ?>" onblur="$.ajax({type:'GET', url:'../client/plugins/optlibre/gestoptlibre.php', data:'action=optlibremodifier&lang=<?php echo $lang; ?>&id=<?php echo $row_optlibre->id; ?>' + '&prix=' + this.value, success:function(html){optlibre_rafraichir();}});" value="<?php echo $row_optlibre->prix; ?>" size="6" class="form" />  &euro;</li>
				<li class="cellule_prix"  style="padding: 5px 0 0 5px;"><input type="text" name="optlibreprix2_<?php echo $row_optlibre->id; ?>" onblur="$.ajax({type:'GET', url:'../client/plugins/optlibre/gestoptlibre.php', data:'action=optlibremodifier&lang=<?php echo $lang; ?>&id=<?php echo $row_optlibre->id; ?>' + '&prix2=' + this.value, success:function(html){optlibre_rafraichir();} });" value="<?php echo $row_optlibre->prix2; ?>" size="6" class="form" />  &euro;</li>
		    <li class="cellule_prix"  style="padding: 5px 0 0 5px;"><a href="#" onclick="optlibre_supprimer(<?php echo $row_optlibre->id; ?>);return false;" class="lien05">x</a></li>
				<li class="cellule" style="width:480px; padding: 5px 0 0 5px;"><textarea name="option_<?php echo $row_optlibre->id; ?>" id="option_<?php echo $row_optlibre->id; ?>" onblur="$.ajax({type:'GET', url:'../client/plugins/optlibre/gestoptlibre.php', data:'action=optlibremodifier&lang=<?php echo $lang; ?>&id=<?php echo $row_optlibre->id; ?>' + '&option=' + this.value });" cols="40" rows="2" class="form_long"><?php echo $optlibredesc->option; ?></textarea></li>
		</ul>
	
   <?php
   	}
   ?>
    <ul class="lignesimple" style="padding-bottom:32px;">
   <li class="cellule" style="width:495px;"><a href="#" onclick="optlibre_ajouter('<?php echo $_REQUEST['ref']; ?>');return false;" class="txt_vert_11">AJOUTER UNE LIGNE</a></li>
   <li class="cellule"  style="width:60px;padding: 5px 0 0 5px;"><a href="#" onclick="optlibre_supprimertout('<?php echo $_REQUEST['ref']; ?>');return false;" class="lien05">tout supprimer</a></li>
   </ul>
<?php
 }

	/*$query = "select sum(stock) as total from $optlibre->table where ref=\"" . $optlibre->ref . "\"";
	$resul = mysql_query($query, $optlibre->link);
	
	$produit = new Produit();
	$produit->charger($optlibre->ref);
	$produit->stock = mysql_result($resul, 0, "total");
	$produit->maj();*/
	
?>



