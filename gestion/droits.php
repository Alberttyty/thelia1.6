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

if(!isset($lang)) $lang=$_SESSION["util"]->lang;

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>

<script type="text/javascript">
	function changer_droits_admin(autorisation, mode, valeur){
            if(valeur != "")
                valeur = 1;
            else
                valeur = 0;

            $.ajax({type:'GET', url:'ajax/droits.php', data:'type_droit=1&autorisation='+autorisation+'&administrateur=<?php echo $_GET['id']; ?>' + '&mode=' + mode + '&valeur=' + valeur})
	}

	function changer_droits_module(module, valeur){
            if(valeur == true)
                valeur = 1;
            else
                valeur = 0;

            $.ajax({type:'GET', url:'ajax/droits.php', data:'type_droit=2&module='+module+'&administrateur=<?php echo $_GET['id']; ?>' + '&valeur=' + valeur})

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
   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a>  <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="droits.php" class="lien04"><?php echo trad('Gestion_droit', 'admin'); ?></a></p>

<!-- bloc dŽclinaisons / colonne gauche -->

<br />
	<div class="titre">
		<select onchange="location='droits.php?id=' + this.value">
			<option value=""><?php echo trad('Select_administrateur', 'admin'); ?></option>
			<?php
				$administrateur = new Administrateur();
				$query = "select * from $administrateur->table where profil<>1";
				$resul = mysql_query($query, $administrateur->link);
				while($row = mysql_fetch_object($resul)){
			?>
				<option value="<?php echo $row->id; ?>" <?php if(isset($_GET['id']) && $_GET['id'] == $row->id) { ?> selected="selected" <?php } ?>><?php echo $row->identifiant; ?></option>
			<?php
				}
			?>
		</select>
	</div>

	<br /><br />

<?php
	if($_GET['id']){
?>
<div class="entete_liste_config">
	<div class="titre"><?php echo trad('Droits_generaux', 'admin'); ?></div>
</div>
<ul class="Nav_bloc_description">
		<li style="height:25px; width:258px;"><?php echo trad('Autorisation', 'admin'); ?></li>
		<li style="height:25px; width:517px; border-left:1px solid #96A8B5;"><?php echo trad('Description', 'admin'); ?></li>
		<li style="height:25px; width:55px; border-left:1px solid #96A8B5;"><?php echo trad('Acces', 'admin'); ?></li>
</ul>
<div class="bordure_bottom">
 	<?php

	$autorisation = new Autorisation();

 	$query = "select * from $autorisation->table";
  	$resul = mysql_query($query, $autorisation->link);
  	$i=0;
  	while($row = mysql_fetch_object($resul)){
			if(!($i%2)) $fond="ligne_claire_rub";
  			else $fond="ligne_fonce_rub";
  			$i++;

			$autorisationdesc = new Autorisationdesc();
			$autorisationdesc->charger($row->id, $lang);

			$autorisation_administrateur = new Autorisation_administrateur();
			$autorisation_administrateur->charger($row->id, $_GET['id']);
 	 ?>
		<ul class="<?php echo $fond; ?>">
			<li style="width:250px;"><?php echo $autorisationdesc->titre; ?></li>
			<li style="width:510px; border-left:1px solid #96A8B5;"><?php echo $autorisationdesc->description; ?></li>
			<li style="width:47px; border-left:1px solid #96A8B5;"><input type="checkbox" onchange="changer_droits_admin(<?php echo $row->id; ?>, 'lecture', this.checked)" <?php if($autorisation_administrateur->lecture) { ?> checked="checked" <?php } ?> /></li>
		</ul>
	 <?php } ?>

	<br />

	<div class="entete_liste_config">
		<div class="titre"><?php echo trad('Droits_modules', 'admin'); ?></div>
	</div>
	<ul class="Nav_bloc_description">
			<li style="height:25px; width:258px;"><?php echo trad('Module', 'admin'); ?></li>
			<li style="height:25px; width:517px; border-left:1px solid #96A8B5;"><?php echo trad('Description', 'admin'); ?></li>
			<li style="height:25px; width:55px; border-left:1px solid #96A8B5;"><?php echo trad('Acces', 'admin'); ?></li>
	</ul>

	<div class="bordure_bottom">
	<?php
		$liste = ActionsAdminModules::instance()->lister(false, true);

		$idx = 0;

		foreach($liste as $module) {

			$autorisation_modules = new Autorisation_modules();
			$autorisation_modules->charger($module->id, $_GET['id']);

			try {
				if (ActionsAdminModules::instance()->est_administrable($module->nom)) {

					$fond="ligne_".($idx%2 ? 'fonce' : 'claire')."_rub";

					$modulesdesc = new Modulesdesc();
					$modulesdesc->charger($module->id);

				 	?>
					<ul class="<?php echo $fond; ?>">
						<li style="width:250px;"><?php echo ActionsAdminModules::instance()->lire_titre_module($module); ?></li>
						<li style="width:510px; border-left:1px solid #96A8B5;"><?php echo $modulesdesc->description; ?></li>
						<li style="width:47px; border-left:1px solid #96A8B5;"><input type="checkbox" onchange="changer_droits_module(<?php echo $module->id; ?>, this.checked)" <?php if($autorisation_modules->autorise) { ?> checked="checked" <?php } ?> /></li>
					</ul>
					<?php

					$idx++;
				}

			} catch (Exception $e) { }
		 }
	?>
	</div>
</div>

<?php
	}
?>

</div>




<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>