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

	if(! est_autorise("acces_commandes")) exit;

	$commande = new Commande();
	$commande->charger_ref($ref);

	/*
	if (file_exists(__DIR__.'/../client/pdf/modeles/facture.php'))
	{

            $commande = new Commande();
            $commande->charger_ref($ref);

            $client = new Client();
            $client->charger_id($commande->client);

            $pays = new Pays();
            $pays->charger($client->pays);

            $zone = new Zone();
            $zone->charger($pays->zone);

            require_once("../client/pdf/modeles/livraison.php");

            $livraison = new Livraison();
            $livraison->creer($ref);

            exit();
	}
	*/

	$nom_fichier_pdf = $commande->livraison . '.pdf';

	// Le moteur ne sortira pas le contenu de $res
	$sortie = false;

	// Le fond est le template de livraison.
  $reptpl = SITE_DIR."client/pdf/template/";
	$fond = "livraison.html";

	$lang = $commande->lang;

	// Compatibilité avec le moteur.
	$_REQUEST['commande'] = $ref;

	require_once(__DIR__ . "/../fonctions/moteur.php");

	Pdf::instance()->generer($res, $nom_fichier_pdf);
?>
