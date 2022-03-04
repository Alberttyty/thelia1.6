<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
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
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
	require_once("pre.php");
	require_once("auth.php");

	if(!isset($liste)) $liste="";
	if(!isset($i)) $i=0;
	if(!isset($id)) $id="";
	if(!isset($lang)) $lang=$_SESSION["util"]->lang;

?>
<?php if(! est_autorise("acces_configuration")) exit; ?>
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

	function tri(critere, order) {
		$.ajax({
			type:"GET",
			url:"ajax/tricarac.php",
			data : "order="+order+"&critere="+critere,
			success : function(html){
				$("#resul").html(html);
				edit();
			}
		});
	}

	function edit(){
		$(".texte_edit").editable("ajax/caracteristique.php", {
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
				tri('classement', 'asc');
			}
		});
	}

	function suppr_carac() {
		return confirm("<?php echo trad("Supprimer définitivement cette caractéristique ?"); ?>");
	}

</script>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	require_once("liste/caracteristique.php");

	require_once("../fonctions/divers.php");?>
<?php
	$menu="configuration";
	require_once("entete.php");
?>

<div id="contenu_int">
   <p align="left"><span class="lien04"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a></span> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="configuration.php" class="lien04"> <?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="caracteristique.php" class="lien04"> <?php echo trad('Gestion_caracteristiques', 'admin'); ?></a></p>

<div class="entete_liste_config">
	<div class="titre"><?php echo trad('LISTE_DES_CARACTERISTIQUES', 'admin'); ?></div>
	<div class="fonction_ajout">
	<form action="caracteristique_modifier.php" id="form_ajout" method="post">
	 	<input type="hidden" name="parent" value="<?php echo($parent); ?>" />
		<input type="hidden" name="id" value="<?php echo($id); ?>" />
	  	<a href="#" onclick="document.getElementById('form_ajout').submit()"><?php echo trad('AJOUTER_UNE_NOUVELLE_CARACTERISTIQUE', 'admin'); ?></a>
	</form>
	</div>
</div>


<ul id="Nav">
		<li style="height:25px; width:777px; border-left:1px solid #96A8B5;"><?php echo trad('Titre_caracteristique', 'admin'); ?></li>
		<li style="height:25px; width:44px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:78px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;">
			<?php echo trad('Classement', 'admin'); ?>
			<ul class="Menu">
				<li><a href="javascript:tri('classement','ASC')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li><a href="javascript:tri('classement','DESC')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:44px; border-left:1px solid #96A8B5;"><?php echo trad('Suppr', 'admin'); ?></li>
</ul>

 <div class="bordure_bottom" id="resul">
	<?php lister_caracteristiques('classement', 'asc') ?>
</div>

</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>