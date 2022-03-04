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

// calcul du port
function port($type = 0, $_pays = false, $_cpostal = "") {

    if ($_SESSION['navig']->commande->transport == "" && !$type)
        return - 1;

    if ($_SESSION['navig']->adresse != 0)
        $chadr = 1;
    else
        $chadr = 0;

    $modules = new Modules();

    if (!$type)
        $modules->charger_id($_SESSION['navig']->commande->transport);
    else
        $modules->charger_id($type);

    if ($modules->type != Modules::TRANSPORT || !$modules->actif)
        return - 1;

    $p = new Pays();

    if ($chadr) {
        $adr = new adresse();
        $adr->charger($_SESSION['navig']->adresse);
        $p->charger($adr->pays);
        $cpostal = $adr->cpostal;
    }
    else {
        $p->charger($_SESSION['navig']->client->pays);
        $cpostal = $_SESSION['navig']->client->cpostal;
    }

	// Prise en compte des infos passée, si aucune des infos précédentes n'est pertinente.
    if (empty($p->id) && $_pays)
        $p->charger($_pays);
    if (empty($cpostal) && $_cpostal != "")
        $cpostal = $_cpostal;

    $zone = new Zone();
    $zone->charger($p->zone);

    try {
        $port = ActionsModules::instance()->instancier($modules->nom);

        $port->nbart = $_SESSION['navig']->panier->nbart();
        $port->poids = $_SESSION['navig']->panier->poids();
        $port->total = $_SESSION['navig']->panier->total();
        $port->zone = $p->zone;
        $port->pays = $p->id;
        $port->unitetr = $zone->unite;
        $port->cpostal = $cpostal;

        $frais = $port->calcule();
        ActionsModules::instance()->appel_module("port", $frais , $port);
        return $frais;

    }
    catch (exception $e) {
        return - 1;
    }
}

?>
