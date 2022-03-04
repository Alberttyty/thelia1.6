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

	/* Substitutions de type client */

	function substitclient($texte){

	   	// Les #CLIENT_RAISONnF
   		$raisons = CacheBase::getCache()->query("select id from raison");

   		if ($raisons) foreach($raisons as $raison) {

   			$sel = ($_SESSION['navig']->client->raison == $raison->id) ? 'selected="selected"' : '';

			$texte = str_replace("#CLIENT_RAISON".$raison->id."F", $sel, $texte);
   		}

		$paysdesc = new Paysdesc();
		$paysdesc->charger($_SESSION['navig']->client->pays);

		$raisondesc = new Raisondesc();
		$raisondesc->charger($_SESSION['navig']->client->raison);

		$texte = str_replace("#CLIENT_IDPAYS", $_SESSION['navig']->client->pays, $texte);
		$texte = str_replace("#CLIENT_ID", intval($_SESSION['navig']->client->id), $texte);
		$texte = str_replace("#CLIENT_REF", $_SESSION['navig']->client->ref, $texte);
		$texte = str_replace("#CLIENT_RAISONID", $_SESSION['navig']->client->raison, $texte);
		$texte = str_replace("#CLIENT_RAISON", $raisondesc->long, $texte);
		$texte = str_replace("#CLIENT_ENTREPRISE", $_SESSION['navig']->client->entreprise, $texte);
		$texte = str_replace("#CLIENT_SIRET", $_SESSION['navig']->client->siret, $texte);
		$texte = str_replace("#CLIENT_INTRACOM", $_SESSION['navig']->client->intracom, $texte);
		$texte = str_replace("#CLIENT_NOM", $_SESSION['navig']->client->nom, $texte);
		$texte = str_replace("#CLIENT_PRENOM", $_SESSION['navig']->client->prenom, $texte);
		$texte = str_replace("#CLIENT_ADRESSE1", $_SESSION['navig']->client->adresse1, $texte);
		$texte = str_replace("#CLIENT_ADRESSE2", $_SESSION['navig']->client->adresse2, $texte);
		$texte = str_replace("#CLIENT_ADRESSE3", $_SESSION['navig']->client->adresse3, $texte);
		$texte = str_replace("#CLIENT_CPOSTAL", $_SESSION['navig']->client->cpostal, $texte);
		$texte = str_replace("#CLIENT_VILLE", $_SESSION['navig']->client->ville, $texte);
		$texte = str_replace("#CLIENT_PAYS", $paysdesc->titre, $texte);
		$texte = str_replace("#CLIENT_EMAIL", $_SESSION['navig']->client->email, $texte);
		$texte = str_replace("#CLIENT_TELFIXE", $_SESSION['navig']->client->telfixe, $texte);
		$texte = str_replace("#CLIENT_TELPORT", $_SESSION['navig']->client->telport, $texte);
		$texte = str_replace("#CLIENT_TYPE", $_SESSION['navig']->client->type, $texte);

		return $texte;
	}
?>
