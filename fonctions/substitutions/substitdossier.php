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

	/* Substitutions de type dossier */

	function substitdossier($texte){
		global $id_dossier;

		$tdossier = new Dossier();
		$tdossierdesc = new Dossierdesc();
		$url = "";

		if($id_dossier) {
			if ($tdossier->charger($id_dossier)) {
				$tdossierdesc->charger($tdossier->id);
				$url = $tdossierdesc->getUrl();
			}
		}
		
		$racine = new Dossier();
		$racine->charger($tdossier->id);
		
		while($racine->parent)
			$racine->charger($racine->parent);

		$texte = str_replace("#DOSSIER_URL", $url, $texte);

		$texte = str_replace("#DOSSIER_CHAPO", "$tdossierdesc->chapo", $texte);
		$texte = str_replace("#DOSSIER_DESCRIPTION", "$tdossierdesc->description", $texte);
		$texte = str_replace("#DOSSIER_POSTSCRIPTUM", "$tdossierdesc->postscriptum", $texte);
		$texte = str_replace("#DOSSIER_ID", "$tdossier->id", $texte);
		$texte = str_replace("#DOSSIER_NOM", "$tdossierdesc->titre", $texte);
		$texte = str_replace("#DOSSIER_PARENT", "$tdossier->parent", $texte);
		$texte = str_replace("#DOSSIER_RACINE", "$racine->id", $texte);

		return $texte;
	}

?>
