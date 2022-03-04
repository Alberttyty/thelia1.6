<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("degressif");

?>
<script type="text/javascript">
function degressif_supprimer(id){
  
}

function degressif_ajouter(ref){
}
</script>

<?php
	include_once(realpath(dirname(__FILE__)) . "/Degressif.class.php");
?>

<?php
	$degressif = new Degressif();
	
	$query_degressif = "select * from $degressif->table where ref=\"". $_REQUEST['ref'] . "\" order by tranchemin";
	$resul_degressif = mysql_query($query_degressif, $degressif->link);
?>
<br />

<div id="degressif_liste">
		<div class="entete">
			<div class="titre" style="cursor:pointer" onclick="$('#pliantdegressivite').show('slow');">PRIX DEGRESSIF</div>
      <div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()">VALIDER LES
            MODIFICATIONS</a></div>
		</div>
<div class="blocs_pliants_prod" id="pliantdegressivite">
   <table width="589" border="0" cellpadding="5" cellspacing="0">
      <tr>
        <td width="246" height="30" class="titre_cellule">Tranche min</td>
        <td width="444" class="titre_cellule">Tranche max</td>   
        <td width="444" class="titre_cellule">Prix</td>
        <td width="444" class="titre_cellule">Prix promo</td>
   	    <td width="300" class="titre_cellule">&nbsp;</td>
      </tr>  
          
    <?php
      $i=0;	
    	while($row_degressif = mysql_fetch_object($resul_degressif)){
    ?>
      <tr class="valeurs">
		    <td width="444" class="titre_cellule"><input type="text" name="degressif_tranchemin_<?php echo $i ?>" value="<?php echo $row_degressif->tranchemin; ?>" size="20" /></td>
	  		<td width="444" class="titre_cellule"><input type="text" name="degressif_tranchemax_<?php echo $i ?>" value="<?php echo $row_degressif->tranchemax; ?>" size="20" /></td>
    		<td width="444" class="titre_cellule"><input type="text" name="degressif_prix_<?php echo $i ?>" value="<?php echo $row_degressif->prix; ?>" size="6" />  &euro;</td>
    	 	<td width="444" class="titre_cellule"><input type="text" name="degressif_prix2_<?php echo $i ?>" value="<?php echo $row_degressif->prix2; ?>" size="6" />  &euro;</td>
   		  <td width="300" class="titre_cellule"><input type="hidden" name="degressif_index_<?php echo $i ?>" value="<?php echo $i ?>" size="6" /><a href="#" onclick="if(confirm('Etes-vous sur de vouloir supprimer cette ligne ?')){$(this).closest('tr').remove();}return false;" class="degressif_supprimer"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></td>
      
      </tr>
   <?php
    $i=$i+1;	
   	}
   ?>
      <tr class="valeurs" style="display:none;">
		    <td width="444" class="titre_cellule"><input type="text" name="degressif_tranchemin_" value="" size="20" /></td>
	  		<td width="444" class="titre_cellule"><input type="text" name="degressif_tranchemax_" value="" size="20" /></td>
    		<td width="444" class="titre_cellule"><input type="text" name="degressif_prix_" value="" size="6" />  &euro;</td>
    	 	<td width="444" class="titre_cellule"><input type="text" name="degressif_prix2_" value="" size="6" />  &euro;</td>
   		  <td width="300" class="titre_cellule"><input type="hidden" name="degressif_index_" value="" /><a href="#" onclick="if(confirm('Etes-vous sur de vouloir supprimer cette ligne ?')){$(this).closest('tr').remove();}return false;" class="degressif_supprimer"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></td>
      </tr>
   </table>
   <a href="#" class="txt_vert_11 degressif_ajouter">+ AJOUTER UNE TRANCHE</a>
</div>
</div>

  <br />
