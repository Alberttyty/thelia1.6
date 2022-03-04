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

    require_once("liste/zone.php");
?>
<?php

	if($_POST['action'] == "ajouter" && $_POST['nomzone'] != ""){
        $zone = new Zone();
		$zone->nom = $_POST['nomzone'];
		$id = $zone->add();
	}

	else if($_GET['action'] == "supprimer" && $_GET['id'] != ""){

        $zone = new Zone();
		$pays = new Pays();

        $query = "update $pays->table set zone=\"-1\" where zone=\"" . $_GET['id'] . "\"";
        $resul = mysql_query($query, $pays->link);
        $zone->charger($_GET['id']);
        $zone->delete();
	}

	if($_REQUEST['id'] != "")
		$id = $_REQUEST['id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>
<?php require_once("js/zone.php"); ?>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="configuration";
	require_once("entete.php");
?>
<div id="contenu_int">
    <p align="left"><a href="index.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="configuration.php" class="lien04"> <?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04"><?php echo trad('Gestion_zones_livraison', 'admin'); ?> </a></p>

<!-- Début de la colonne de gauche -->
<div id="bloc_description">
<div class="bordure_bottom">
<!-- liste des zones -->
		<div class="entete_liste_config">
			<div class="titre"><?php echo trad('LISTE_ZONES', 'admin'); ?></div>
		</div>
<?php

		$zone = new Zone();
		$query = "select * from $zone->table";
		$resul = mysql_query($query, $zone->link);

		$i = 0;

		while($row = mysql_fetch_object($resul)){
		if($i%2)
			$fond = "ligne_fonce_BlocDescription";
		else
			$fond = "ligne_claire_BlocDescription";

?>
		<ul class="<?php echo $fond; ?>">
			<li style="width:460px;"><?php echo $row->nom; ?></li>
			<li style="width:40px;"><a href="zone.php?action=editer&id=<?php echo $row->id; ?>#zone"><?php echo trad('editer', 'admin'); ?></a></li>
			<li><a href="zone.php?action=supprimer&id=<?php echo $row->id; ?>"><?php echo trad('supprimer', 'admin'); ?></a></li>
		</ul>
<?php
		$i++;
	}
?>
</div>
<!-- fin lites zones -->
<!-- bloc modification d'une zone -->
<a id="zone"></a>
<div class="bordure_bottom" id="listepays">
<?php

	if($id != ""){
		modifier_pays_zone($id);
	}
?>
</div>
<!-- fin du bloc modification d'une zone -->
</div>
<!-- fin du bloc description -->

<!-- bloc colonne de droite -->
<div id="bloc_colonne_droite">

<!-- bloc d'ajout d'une zone -->
<form action="zone.php" method="post" id="formaj">
<input type="hidden" name="action" value="ajouter" />
	<div class="bordure_bottom" id="ajout_zone">
		<div class="entete_config">
			<div class="titre"><?php echo trad('AJOUTER_ZONE', 'admin'); ?></div>
		</div>
		<ul class="ligne1">
				<li style="width:260px;">
					<input type="text" name="nomzone" class="form_inputtext" onclick="this.value=''" value="Nom de la zone" />
				</li>
				<li><a href="javascript:document.getElementById('formaj').submit()"><?php echo trad('AJOUTER', 'admin'); ?></a></li>
		</ul>
	</div>
</form>
<!-- fin du bloc d'ajout d'une zone -->
</div>
<!-- fin du bloc colonne de droite -->
</div>
<?php require_once("pied.php"); ?>
</div>
</div>
</body>
</html>
