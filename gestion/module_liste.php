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

if(! est_autorise("acces_modules")) exit; 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="plugins";
	require_once("entete.php");
?>

<div id="contenu_int">
     <p align="left">
     	<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a>
		<img src="gfx/suivant.gif" width="12" height="9" border="0" />
		<a href="#" class="lien04"><?php echo trad('Liste_modules', 'admin'); ?></a>
    </p>

<div id="bloc_informations">
	<ul style="width: 50%">
	<li class="entete_configuration" style="width: 445px"><?php echo trad('LISTE_MODULES', 'admin'); ?></li>

	<?php

	$liste = ActionsAdminModules::instance()->lister(false, true);

	$i=0;

	foreach($liste as $module) {

		if(! $module->est_autorise()) continue;

  		try {
			ActionsAdminModules::instance()->trouver_fichier_admin($module->nom);

			$titre = ActionsAdminModules::instance()->lire_titre_module($module);

			if(!($i%2)) $fond="fonce";
	  		else $fond="claire";

			?>
			   	<li class="<?php echo($fond); ?>" style="width:390px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo $titre; ?></li>
				<li class="<?php echo($fond); ?>" style="width:50px;"><a href="module.php?nom=<?php echo $module->nom; ?>"><?php echo trad('editer', 'admin'); ?> </a></li>
			<?php

			$i++;

  		} catch (Exception $ex) {
  			// echo $ex;
  		}
	}
?>
</ul>
</div>
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>