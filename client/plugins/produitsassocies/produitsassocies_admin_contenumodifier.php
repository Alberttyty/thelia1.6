<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("produitsassocies");

require_once(realpath(dirname(__FILE__)) . "/Produitsassocies.class.php");
require_once(realpath(dirname(__FILE__)) . "/liste/produitsassocies.php");


?>
<!-- début du bloc de gestion des produits associés -->
<div class="entete">
	<div class="titre" style="cursor:pointer" onclick="$('#pliantproduitsassocies').show('slow');"><?php echo trad('GESTION_PRODUITS_ASSOCIES', 'admin'); ?></div>
</div>
<div class="blocs_pliants_prod" id="pliantproduitsassocies">
	<ul class="ligne1">
		<li class="cellule">
		<select class="form_select" id="produitsassocies_rubrique" onchange="charger_listproduitsassocies(this.value);">
     	<option value="">&nbsp;</option>
     	<?php
 				echo arbreOption(0, 1, 0, 0);
		?>
		</select></li>

		<li class="cellule">
			<select class="form_select" id="select_prodasso">
				<option value="">&nbsp;</option>
			</select>
		</li>
		<li class="cellule"><a href="javascript:produitsassocies_ajouter($('#select_prodasso').val())"><?php echo trad('AJOUTER', 'admin'); ?></a></li>
	</ul>

	<ul id="produitsassocies_liste">
		<?php
		lister_produitsassocies("contenu",$_GET['id']);
    ?>
	 </ul>
<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantproduitsassocies').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>
<!-- fin du bloc de gestion des produits associés -->