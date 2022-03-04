<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
require_once("pre.php");
require_once("auth.php");

if(! est_autorise("acces_catalogue")) exit; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>

<script src="../lib/jquery/jeditable.js" type="text/javascript"></script>
<script src="../lib/jquery/menu.js" type="text/javascript"></script>

<script type="text/javascript">

	$(document).ready(function() {
    	edit();
	});

	function edit(){
		$(".texte_edit").editable("ajax/produit.php", {
			  loadurl : "ajax/load.php",
		      select : true,
		      onblur: "submit",
		      cssclass : "ajaxedit",
			  width: '100%',
			  placeholder: "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
			  tooltip   : "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
			  indicator : '<img src="gfx/indicator.gif" />'
		});

		$(".classement_edit").editable("ajax/classement.php", {
		      select : true,
		      onblur: "submit",
		      cssclass : "ajaxedit",
		      placeholder: "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
		      tooltip   : "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
		      indicator : '<img src="gfx/indicator.gif" />',
		      callback : function(value, settings){
				var repere = value.split("|");

				if(repere[0] == "produit")
					tri('ASC','<?php echo $parent; ?>','2','classement','');
				else if(repere[0] == "rubrique")
					tri('ASC','<?php echo $parent; ?>','1','classement','');
			}
		  });
	}

	function supprimer_produit(ref, parent){
    ref=encodeURIComponent(ref);
		if(confirm("Voulez-vous vraiment supprimer ce produit ?")) location="produit_modifier.php?ref=" + ref + "&action=supprimer&parent=" + parent;

	}

	function supprimer_rubrique(id, parent){
		if(confirm("Voulez-vous vraiment supprimer cette rubrique et tous son contenu (produits et sous-rubriques) ?")) location="rubrique_modifier.php?id=" + id + "&action=supprimer&parent=" + parent;

	}

	function checkvalues(type, id) {
		$.ajax({
			url:'ajax/produit.php',
			data:{
				id: type+'_'+id
			}
		});
	}

	function check(nom,type,parent,modif){

		$.ajax({
			url:'ajax/produit.php',
			data: {
				id: type,
				parent: parent,
				modif: modif
			},
			success: function() {
				if(modif == 0)
					$('input[id^='+nom+']').removeAttr("checked");
				else
					$('input[id^='+nom+']').attr("checked", "checked");
			}
		});
	}

	function tri(order,ref,type,critere,alpha) {
		$.ajax({
			type:"GET",
			url:"ajax/tri.php",
			data : "ref="+ref+"&order="+order+"&type="+type+'&critere='+critere+"&alpha="+alpha,
			success : function(html){
				if(type == "1")
					$("#resulrubrique").html(html);
				else
					$("#resulproduit").html(html);

				edit();
			}
		})
	}
</script>
</head>
<body>

<div id="wrapper">
<div id="subwrapper">

<?php
	require_once("../fonctions/divers.php");

	require_once("liste/rubrique.php");
	require_once("liste/produit.php");

	$menu="catalogue";
	require_once("entete.php");

	if(!isset($parent)) $parent="";
	if(!isset($id)) $id="";
	if(!isset($classement)) $classement="";
?>

<div id="contenu_int">
	<p align="left">

		<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a>
		<img src="gfx/suivant.gif" width="12" height="9" border="0" />
		<a href="parcourir.php" class="lien04"><?php echo trad('Gestion_catalogue', 'admin'); ?></a>

	    <?php
        $parentdesc = new Rubriquedesc();

		$parentdesc->charger($parent);
		$parentnom = $parentdesc->titre;

		$res = chemin_rub($parent);
		$tot = count($res)-1;
		?>

		<?php if($parent) { ?>
			<img src="gfx/suivant.gif" width="12" height="9" border="0" />
		<?php
		}

		while($tot --) {
		?>
			<a href="#" onclick="document.getElementById('formulaire').submit()"></a> <a href="parcourir.php?parent=<?php echo($res[$tot+1]->rubrique); ?>" class="lien04"> <?php echo($res[$tot+1]->titre); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
   		<?php
    	}

        $parentdesc = new Rubriquedesc();

		if ($parent)
			$parentdesc->charger($parent);
		else
			$parentdesc->charger($id);

		$parentnom = $parentdesc->titre;
		?>
	 	<a href="parcourir.php?parent=<?php echo($parentdesc->rubrique); ?>" class="lien04"> <?php echo($parentdesc->titre); ?></a>
		<?php
			if ($parent != 0) {
				?>
				<a class="lien04" href="rubrique_modifier.php?id=<?php echo $parent ?>">(<?php echo trad('editer', 'admin'); ?>)</a>
				<?php
			}
		?>
	 	</p>

	<div class="entete_liste">
		<div class="titre"><?php echo trad('LISTE_RUBRIQUES', 'admin'); ?></div><div class="fonction_ajout"><a href="rubrique_modifier.php?parent=<?php echo($parent); ?>"><?php if($parent == "") { ?><?php echo trad('AJOUTER_RUBRIQUE', 'admin'); ?><?php } else {?><?php echo trad('AJOUTER_SOUSRUBRIQUE', 'admin'); ?><?php } ?></a></div>
	</div>

	<ul id="Nav">
		<li style="height:25px; width:630px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;  cursor: pointer;">
			<?php echo trad('Titre_rubrique', 'admin'); ?>
			<ul class="Menu">
				<li style="width:591px;"><a href="javascript:tri('ASC','<?php echo $parent; ?>','1','titre','alpha')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li style="width:591px;"><a href="javascript:tri('DESC','<?php echo $parent; ?>','1','titre','alpha')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>

		<li style="height:25px; width:60px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
			<?php echo trad('En_ligne', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:check('rub_ligne','lignetousrub','<?php echo $parent; ?>',1)"><?php echo trad('Tout_cocher', 'admin'); ?></a></li>
				<li><a href="javascript:check('rub_ligne','lignetousrub','<?php echo $parent; ?>',0)"><?php echo trad('Tout_decocher', 'admin'); ?></a></li>
			</ul>
		</li>

		<li style="height:25px; width:61px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:41px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:78px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer;">
			<?php echo trad('Classement', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:tri('ASC','<?php echo $parent; ?>','1','classement','')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li><a href="javascript:tri('DESC','<?php echo $parent; ?>','1','classement','')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:44px; border-left:1px solid #96A8B5;"><?php echo trad('Suppr', 'admin'); ?></li>
	</ul>

	<div id="resulrubrique">
		<?php
		liste_rubriques($parent, 'classement', 'ASC', '')
		?>
	</div>

	<div class="entete_liste" style="margin-top:20px">
		<div class="titre"><?php echo trad('LISTE_PRODUITS', 'admin'); ?></div>
		<div class="fonction_ajout"><a href="produit_modifier.php?rubrique=<?php echo($parent); ?>"><?php echo trad('AJOUTER_PRODUIT', 'admin'); ?></a></div>
	</div>

	<ul id="Nav2">
		<li style="height:25px; width:44px; border-left:1px solid #96A8B5;"> </li>
		<li style="height:25px; width:68px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; "><?php echo trad('ref', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:tri('ASC','<?php echo $parent; ?>','2','ref','')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li><a href="javascript:tri('DESC','<?php echo $parent; ?>','2','ref','')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:232px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
			<?php echo trad('Titre_produit', 'admin'); ?>
			<ul class="Menu">
				<li style="width:232px;"><a href="javascript:tri('ASC','<?php echo $parent; ?>','2','titre','alpha')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li style="width:232px;"><a href="javascript:tri('DESC','<?php echo $parent; ?>','2','titre','alpha')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>

		<li style="height:25px; width:46px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; "><?php echo trad('Stock', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:tri('ASC','<?php echo $parent; ?>','2','stock','')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li><a href="javascript:tri('DESC','<?php echo $parent; ?>','2','stock','')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:37px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; "><?php echo trad('Prix', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:tri('ASC','<?php echo $parent; ?>','2','prix','')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li><a href="javascript:tri('DESC','<?php echo $parent; ?>','2','prix','')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:75px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; "><?php echo trad('Prix_promo', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:tri('ASC','<?php echo $parent; ?>','2','prix2','')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li><a href="javascript:tri('DESC','<?php echo $parent; ?>','2','prix2','')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:71px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
			<?php echo trad('Promotion', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:check('promo','promotous','<?php echo $parent; ?>',1)"><?php echo trad('Tout_cocher', 'admin'); ?></a></li>
				<li><a href="javascript:check('promo','promotous','<?php echo $parent; ?>',0)"><?php echo trad('Tout_decocher', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:71px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
			<?php echo trad('Nouveaute', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:check('nouveaute','nouveautetous','<?php echo $parent; ?>',1)"><?php echo trad('Tout_cocher', 'admin'); ?></a></li>
				<li><a href="javascript:check('nouveaute','nouveautetous','<?php echo $parent; ?>',0)"><?php echo trad('Tout_decocher', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:60px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
			<?php echo trad('En_ligne', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:check('prod_ligne','lignetousprod','<?php echo $parent; ?>',1)"><?php echo trad('Tout_cocher', 'admin'); ?></a></li>
				<li><a href="javascript:check('prod_ligne','lignetousprod','<?php echo $parent; ?>',0)"><?php echo trad('Tout_decocher', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:48px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:85px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
			<?php echo trad('Classement', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:tri('ASC','<?php echo $parent; ?>','2','classement','')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li><a href="javascript:tri('DESC','<?php echo $parent; ?>','2','classement','')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:44px; border-left:1px solid #96A8B5;"><?php echo trad('Suppr', 'admin'); ?>.</li>
	</ul>

	<div id="resulproduit" class="bordure_bottom">
	<?php
	/*
		AJOUT D'UNE PAGINATION POUR VOYAGES PERSONALISES
	*/
	if($_SERVER['SERVER_NAME'] == 'ecm-voyages.fr' && $parent == 14) {
		liste_produits($parent, 'classement', 'DESC', '',150);
		
		echo('<div class="pagination">');
		
		if(!empty($_GET['page'])) {
			$page = $_GET['page']+0;
		} else {
			$page = 1;
		}
				
		if($page>1) {
			$prev = $page-1;
			if($prev>1) {
				echo('<a style="float:left;" href="parcourir.php?parent='.$parent.'&page='.$prev.'">Précédent</a>');
			} else {
				echo('<a style="float:left;" href="parcourir.php?parent='.$parent.'">Précédent</a>');
			}
		}
		$next = $page+1;
		echo('<a style="float:right;" href="parcourir.php?parent='.$parent.'&page='.$next.'">Suivant</a>');
		
		echo('</div>');
	} else {
		liste_produits($parent, 'classement', 'ASC', '');
	}
	?>
		
	</div>
</div>

<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>