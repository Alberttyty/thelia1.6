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

require_once (__DIR__ . "/../pre.php");
require_once (__DIR__ . "/../auth.php");

require_once (__DIR__ . "/../../fonctions/divers.php");

?>
<?php

if (!est_autorise("acces_catalogue"))
	exit;

?>
<?php

require_once (__DIR__ . "/../liste/contenu_associe.php");


header('Content-Type: text/html; charset=utf-8');

if (isset($_GET["action"]))
	$action = $_GET["action"];
else
	$action = "";


switch ($action) {
	case 'contenu_assoc':
		contenuassoc_contenu();
		break;
	case 'ajouter':
		contenuassoc_ajouter();
		break;
	case 'supprimer':
		contenuassoc_supprimer();
		break;
}

function contenuassoc_contenu() {
	if ($_GET['type'] == 1) {
		$objet = new Produit();
		$objet->charger($_GET['objet']);
	} else {
		$objet = new Rubrique();
		$objet->charger($_GET['objet']);
	}

	$contenu = new Contenu();

	$query = "select * from $contenu->table where dossier=\"" . $_GET['id_dossier'] . "\"";
	$resul = $contenu->query($query);

	while ($resul && $row = $contenu->fetch_object($resul)) {

		$contenuassoc = new Contenuassoc();
		if ($contenuassoc->existe($objet->id, $_GET['type'], $row->id))
			continue;

		$contenudesc = new Contenudesc();
		$contenudesc->charger($row->id);

		?>
			<option value="<?php echo $row->id; ?>"><?php echo $contenudesc->titre; ?></option>
		<?php
	}
}

function contenuassoc_ajouter() {
	if ($_GET['type'] == 1) {
		$objet = new Produit();
		$objet->charger($_GET['objet']);
	} else {
		$objet = new Rubrique();
		$objet->charger($_GET['objet']);
	}

	$contenuassoc = new Contenuassoc();

	$contenuassoc = new Contenuassoc();
	$contenuassoc->objet = $objet->id;
	$contenuassoc->type = $_GET['type'];
	$contenuassoc->contenu = $_GET['id'];
	$contenuassoc->add();

	lister_contenuassoc($_GET['type'], $_GET['objet']);

	if ($contenuassoc->type == 1)
		ActionsModules::instance()->appel_module("modprod", $objet);
	else
		ActionsModules::instance()->appel_module("modrub", $objet);

}

function contenuassoc_supprimer() {
	$contenuassoc = new Contenuassoc();
	$contenuassoc->charger($_GET['id']);
	$contenuassoc->delete();

	if ($contenuassoc->type == 1)
		$objet = new Produit();
	else
		$objet = new Rubrique();

	$objet->charger($contenuassoc->objet);

	if ($contenuassoc->type == 1)
		ActionsModules::instance()->appel_module("modprod", $objet);
	else
		ActionsModules::instance()->appel_module("modrub", $objet);

	lister_contenuassoc($_GET['type'], $_GET['objet']);
}
?>