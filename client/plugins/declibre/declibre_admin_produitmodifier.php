<?php
  
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/Declibre.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
  
  autorisation("declibre");

  if(!isset($_SESSION["util"]->id)) exit;
  
  global $lang; 	
  if(!isset($lang)) $lang=$_SESSION["util"]->lang;
  if($lang=="") $lang = 1;
		
	$declibre = new Declibre();
	
	$query_declibre = "select * from $declibre->table where ref=\"". $_REQUEST['ref'] . "\"";
	$resul_declibre = mysql_query($query_declibre, $declibre->link);
	
	$declibreintitule = new Declibreintitule();   
	$declibreintitule->charger_ref($_REQUEST['ref']);
  
  $declibreintituledesc = new Declibreintituledesc();   
	$declibreintituledesc->charger($declibreintitule->id,$lang);
  if($declibreintituledesc->declibreintitule=="")$declibreintituledesc->charger($declibreintitule->id,1);     
	
?>

<!-- début du bloc de gestion des déclinaisons libre -->
<div id="declibre">
	<div class="entete">
		<div class="titre" style="cursor:pointer" onclick="$('#pliantdeclinaisonslibres').show('slow');">GESTION DES DECLINAISONS LIBRES</div>
		<div class="fonction_valider"><a href="#" onclick="envoyer()">VALIDER LES MODIFICATIONS</a></div>
	</div>
  
  <div class="blocs_pliants_prod" id="pliantdeclinaisonslibres">		

    <ul class="lignesimple">
    <li class="cellule" style="width:340px; padding: 5px 0 0 5px;">Intitulé 1 : <input type="text" name="declibreintitule1" value="<?php echo $declibreintituledesc->titre1 ?>" size="33" class="form" /></li>
    </ul>
    <ul class="lignesimple" style="margin-bottom:5px;">
    <li class="cellule" style="width:340px; padding: 5px 0 0 5px;">Intitulé 2 : <input type="text" name="declibreintitule2" value="<?php echo $declibreintituledesc->titre2 ?>" size="33" class="form" /></li>
    </ul>

    <div id="declibre_liste">
    
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
       <li class="cellule" style="width:395px;"><a href="#" onclick="declibre_ajouter('<?php echo $_REQUEST['ref']; ?>');return false;" class="txt_vert_11">AJOUTER UNE LIGNE</a></li>
       <li class="cellule"  style="float:right;width:100px;text-align:right;width:100px;padding: 5px 5px 0 5px;"><a href="#" onclick="declibre_supprimertout('<?php echo $_REQUEST['ref']; ?>');return false;" class="lien05">tout supprimer</a></li>
       </ul>
       <ul class="lignesimple">
       <li class="cellule" style="width:160px;">Valeurs 1</li>
       <li class="cellule" style="width:160px;">Valeurs 2</li>
       <li class="cellule" style="width:220px;"></li>
       </ul>
       <ul class="lignesimple">
       <li class="cellule" style="width:160px;"><input type="text" id="declibrecombinaison_1" name="declibrecombinaison_1" size="20" class="form" /></li>
       <li class="cellule" style="width:160px;"><input type="text" id="declibrecombinaison_2" name="declibrecombinaison_2" size="20" class="form" /></li>
       <li class="cellule" style="width:220px;"><a href="#" onclick="declibre_combinaisons('<?php echo $_REQUEST['ref']; ?>');return false;" class="txt_vert_11">CREER LES COMBINAISONS</a></li>
       </ul>	
       <ul class="lignesimple">
       <li class="cellule" style="width:500px;">Saisir des listes de valeurs s&eacute;par&eacute;es par une virgule.</li>
       </ul>
       <ul class="lignesimple">
       <li class="cellule" style="width:500px;">Exemple : Valeurs 1 = Noir, Vert, Rouge et Valeurs 2 = S, M, L, XL</li>
       </ul>
    </div>
    <div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantdeclinaisonslibres').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
    
  </div>
  
</div>    