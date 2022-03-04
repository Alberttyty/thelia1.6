<?php
        if(!isset($_SESSION["util"]->id)) exit;

?>
<script type="text/javascript">
function optlibre_supprimer(id){
	if(confirm("Validez vous cette suppression ?"))
		$.ajax({type:'GET', cache:false, url:'../client/plugins/optlibre/gestoptlibre.php', data:'action=optlibresupprimer&id=' + id, success:function(html){$('#optlibre_liste').html(html); optlibre_rafraichir();}})
}

function optlibre_supprimertout(ref){
	if(confirm("Voulez vous tout supprimer ?"))
		$.ajax({type:'GET', cache:false, url:'../client/plugins/optlibre/gestoptlibre.php', data:'action=optlibresupprimertout&ref=' + ref, success:function(html){$('#optlibre_liste').html(html); optlibre_rafraichir();}})
}

function optlibre_ajouter(ref){
		$.ajax({type:'GET', cache:false, url:'../client/plugins/optlibre/gestoptlibre.php', data:'action=optlibreajouter&ref=' + ref, success:function(html){$('#optlibre_liste').html(html)}})
}

function optlibre_rafraichir(){
	/*$.ajax({type:'GET', cache:false, url:'../client/plugins/optlibre/stockprod.php', data:'ref=<?php echo $_GET['ref']; ?>',success:function(html){$('#stock').val(html)}})
	$.ajax({type:'GET', cache:false, url:'../client/plugins/optlibre/prixprod.php', data:'ref=<?php echo $_GET['ref']; ?>&prix=1',success:function(html){$('#prix').val(html)}})
	$.ajax({type:'GET', cache:false, url:'../client/plugins/optlibre/prixprod.php', data:'ref=<?php echo $_GET['ref']; ?>&prix=2',success:function(html){$('#prix2').val(html)}})*/
}


</script>

<?php
	include_once(realpath(dirname(__FILE__)) . "/Optlibre.class.php");
  include_once(realpath(dirname(__FILE__)) . "/classes/Optlibredesc.class.php");
?>

<?php
  global $lang; 	
  if(!isset($lang)) $lang=$_SESSION["util"]->lang;
  if($lang=="") $lang = 1;
  
	$optlibre = new Optlibre();
  $optlibredesc = new Optlibredesc();
	$query_optlibre = "select $optlibre->table.id,$optlibre->table.ref,$optlibre->table.prix,$optlibre->table.prix2 from $optlibre->table where ref=\"". $_REQUEST['ref'] . "\"";
	$resul_optlibre = mysql_query($query_optlibre, $optlibre->link);
?>

<!-- début du bloc de gestion des déclinaisons libre -->
		<div class="entete">
			<div class="titre" style="cursor:pointer" onclick="$('#pliantoptionslibres').show('slow');">GESTION DES OPTIONS LIBRES</div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('titre').focus();envoyer();">VALIDER LES MODIFICATIONS</a></div>
		</div>
<div class="blocs_pliants_prod" id="pliantoptionslibres">		

<div id="optlibre_liste">

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
</div>
<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantoptionslibres').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>

</div>

  <br />
