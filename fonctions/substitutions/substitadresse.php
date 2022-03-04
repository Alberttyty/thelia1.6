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

	/* Substitutions de type adresse */

	function substitadresse($texte){
		global $adresse;

		$tadresse = new Adresse();

		if ($tadresse->charger(intval($adresse))) {

			$raisondesc = new Raisondesc();
			$raisondesc->charger($tadresse->raison);

			$paysdesc = new Paysdesc();
			$paysdesc->charger($tadresse->pays);

			$texte = str_replace("#ADRESSE_RAISONID", $tadresse->raison, $texte);
			$texte = str_replace("#ADRESSE_IDPAYS", $tadresse->pays, $texte);

			$texte = str_replace("#ADRESSE_ID", $tadresse->id, $texte);

			$texte = str_replace("#ADRESSE_LIBELLE", $tadresse->libelle, $texte);
			$texte = str_replace("#ADRESSE_RAISON", $raisondesc->long, $texte);
			$texte = str_replace("#ADRESSE_ENTREPRISE", $tadresse->entreprise, $texte);
			$texte = str_replace("#ADRESSE_NOM", $tadresse->nom, $texte);
			$texte = str_replace("#ADRESSE_PRENOM", $tadresse->prenom, $texte);
			$texte = str_replace("#ADRESSE_ADRESSE1", $tadresse->adresse1, $texte);
			$texte = str_replace("#ADRESSE_ADRESSE2", $tadresse->adresse2, $texte);
			$texte = str_replace("#ADRESSE_ADRESSE3", $tadresse->adresse3, $texte);
			$texte = str_replace("#ADRESSE_CPOSTAL", $tadresse->cpostal, $texte);
			$texte = str_replace("#ADRESSE_VILLE", $tadresse->ville, $texte);
			$texte = str_replace("#ADRESSE_PAYS", $paysdesc->titre, $texte);
			$texte = str_replace("#ADRESSE_TEL", $tadresse->tel, $texte);
		}

		$texte = str_replace("#ADRESSE_ACTIVE", "" . $_SESSION['navig']->adresse . "", $texte);

		return $texte;
	}
?>