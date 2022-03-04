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

require_once("../fonctions/divers.php");

require_once("liste/transport.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>
<?php require_once("js/transport.php"); ?>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="configuration";
	require_once("entete.php");
?>

<div id="contenu_int">
    <p><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="configuration.php" class="lien04"> <?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="#" class="lien04"><?php echo trad('Gestion_transports', 'admin'); ?></a></p>

<!-- Début de la colonne de gauche -->
<div id="bloc_description">
	<div class="bordure_bottom">
<!-- bloc des listes de transports -->
		<div class="entete_liste_config">
			<div class="titre"><?php echo trad('LISTE_TRANSPORTS', 'admin'); ?></div>
		</div>

<?php
	$liste = ActionsAdminModules::instance()->lister(2, true);

	$i = 0;

	foreach($liste as $module) {

		$fond ="ligne_".($i%2 ? 'fonce' : 'claire')."_BlocDescription";
?>

		<ul class="<?php echo $fond; ?>">
			<li style="width:534px;"><?php echo ActionsAdminModules::instance()->lire_titre_module($module); ?></li>
			<li style="width:32px;"><a href="transport.php?id=<?php echo $module->id; ?>#lzone"><?php echo trad('editer', 'admin'); ?></a></li>
		</ul>
<?php
		$i++;
	}
?>
</div>
	<!-- fin du bloc des listes de transports -->

	<a name="lzone">&nbsp;</a>
<?php
	if($_GET['id']){
	?>

		<div class="bordure_bottom" id="listezone">
		<?php modifier_transports($_GET['id']) ?>
		</div>
	<?php
	}
?>

</div>
<!-- fin du bloc description -->
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
