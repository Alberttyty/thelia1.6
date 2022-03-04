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
  require_once(__DIR__ . '/../../fonctions/error_reporting.php');
  require_once(__DIR__ . '/../../fonctions/mutualisation.php');
	require_once(__DIR__ . "/../../classes/Navigation.class.php");
	require_once(__DIR__ . "/../../classes/Administrateur.class.php");
	require_once(__DIR__ . "/../../fonctions/modules.php");
	session_start();

	$commande = new Commande();
	$commande->charger_ref($_GET['ref']);

	// Si un client est identifié mais n'est pas celui qui a commandé ou que la commande n'est pas payée
	// ou qu'un admin identifié n'est pas autorisé
  if( !((isset($_SESSION['navig']) && $_SESSION['navig']->connecte && $_SESSION['navig']->client->id == $commande->client && $commande->facture != "")
      || (isset($_SESSION["util"]) && est_autorise("acces_commandes")))) exit;

	/* Compatibilité 1.4 -> On utilise le modèle PDF si il existe
	if (file_exists(__DIR__.'/modeles/facture.php')) {
  		include_once(__DIR__ . "/../../classes/Commande.class.php");
  		include_once(__DIR__ . "/../../classes/Client.class.php");
  		include_once(__DIR__ . "/../../classes/Venteprod.class.php");
  		include_once(__DIR__ . "/../../classes/Produit.class.php");
  		include_once(__DIR__ . "/../../classes/Adresse.class.php");
  		include_once(__DIR__ . "/../../classes/Zone.class.php");
  		include_once(__DIR__ . "/../../classes/Pays.class.php");
  		include_once(__DIR__ . "/../../fonctions/divers.php");

	    $client = new Client();
	  	$client->charger_id($commande->client);

	  	$pays = new Pays();
	  	$pays->charger($client->pays);

	  	$zone = new Zone();
	  	$zone->charger($pays->zone);

  		include_once(__DIR__ . "/modeles/facture.php");

  		$facture = new Facture();
  		$facture->creer($_GET['ref']);

  		exit();
	}*/
	// Le moteur ne sortira pas le contenu de $res
	$sortie = false;

	// Le fond est le template de facture.
  $reptpl=SITE_DIR."client/pdf/template/";
	//$reptpl = FICHIER_URL."client/pdf/template/";
	$fond = "facture.html";

	$lang = $commande->lang;

	// Compatibilité avec le moteur.
	$_REQUEST['commande'] = $_GET['ref'];

	require_once(__DIR__ . "/../../fonctions/moteur.php");
	//require_once(__DIR__ . "/../../classes/Pdf.class.php");
	Pdf::instance()->generer($res, $_GET['ref'] . ".pdf");
?>
