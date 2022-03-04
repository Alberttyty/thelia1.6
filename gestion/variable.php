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

if(! est_autorise("acces_configuration")) exit;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>

<script type="text/javascript">

    function confirmer_suppression() {
        return confirm("<?php echo trad('Supprimer définitivement cette variable ?', 'admin'); ?>");
    }

    function show_form_ajouter(afficher) {
        if (afficher) {
            $('.bouton_ajouter').hide();
            $('.form_ajouter').show();
            $('input[name=ajout_nom]').focus();
        }
        else {
            $('.bouton_ajouter').show();
            $('.form_ajouter').hide();

            $('input[name^=ajout_]').val('');
        }
    }
</script>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="configuration";
	require_once("entete.php");
?>

<div id="contenu_int">
   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="variable.php" class="lien04"><?php echo trad('Gestion_variables', 'admin'); ?></a></p>

<!-- bloc déclinaisons / colonne gauche -->
<div id="bloc_description">
<div class="entete_liste_config">
	<div class="titre"><?php echo trad('LISTE_VARIABLES', 'admin'); ?></div>
	<div class="fonction_valider">
		<a onclick="$('#formvariable').submit(); return false;" href="#"><?php echo trad('VALIDER LES MODIFICATIONS', 'admin'); ?></a>
	</div>
</div>

<ul class="Nav_bloc_description">
		<li style="height:25px; width:194px;"><?php echo trad('Nom2', 'admin'); ?></li>
		<li style="height:25px; width:360px; border-left:1px solid #96A8B5;"><?php echo trad('Valeur', 'admin'); ?></li>
</ul>

<div class="bordure_bottom">
    <form action="variable_modifier.php" id="formvariable" method="post">
	    <input type="hidden" name="action" value="modifier" />

    	<?php
		$variable = new Variable();

	 	$query = "select * from $variable->table where cache='0' order by nom asc";
	  	$resul = $variable->query($query);

	  	$i=0;

	  	while ($resul && $row = $variable->fetch_object($resul)) {

	  		$fond="ligne_".($i++%2 ? "fonce" : "claire")."_BlocDescription";

	  		?>
			<ul class="<?php echo $fond; ?>">
				<li style="width:195px;"><?php echo($row->nom); ?></li>
				<li style="width:360px; border-left:1px solid #96A8B5;">
					<input style="width: 355px;" name="valeur[<?php echo($row->id); ?>]" type="text" class="form" value="<?php echo  htmlspecialchars($row->valeur); ?>" />
				</li>
				<li style="width:10px; border-left:1px solid #96A8B5; text-align: center;">
					<?php if (! $row->protege) { ?>
					<a title="Supprimer cette variable" href="variable_modifier.php?action=supprimer&amp;id=<?php echo($row->id); ?>" onclick="return confirmer_suppression();">
						<img alt="X" src="gfx/supprimer.gif">
					</a>
					<?php } ?>
				</li>
			</ul>
		<?php
	  	}

	  	$fond="ligne_".($i++%2 ? "fonce" : "claire")."_BlocDescription";

	  	?>

		<ul class="bouton_ajouter <?php echo $fond; ?> lignebottom">
			<li style="width:98%; text-align: right; height: 28px">
				<button onclick="show_form_ajouter(true); return false;"><?php echo trad('Ajouter une variable', 'admin'); ?></button>
			</li>
		</ul>

		<div class="form_ajouter" style="display: none;">
			<ul class="<?php echo $fond; ?>">
				<li style="width:195px;"><input type="text" style="width: 190px;" name="ajout_nom" value="" /></li>
				<li style="width:360px; border-left:1px solid #96A8B5;">
					<input type="text" style="width: 355px;" name="ajout_valeur" value="" />
				</li>
				<li style="width:10px; border-left:1px solid #96A8B5; text-align: center;">
					<a onclick="show_form_ajouter(false); return false;" href="#" title="<?php echo trad('Annuler', 'admin'); ?>">
						<img src="gfx/supprimer.gif" alt="X" />
					</a>
				</li>
			</ul>
		</div>
	</form>
</div>
</div>
<!-- fin du bloc de description / colonne de gauche -->
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
