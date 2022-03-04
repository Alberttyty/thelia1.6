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

	/* Substitutions de type rubrique */

	function substitrubriques($texte){
		global $id_rubrique;

		$trubrique = new Rubrique();
		$trubriquedesc = new Rubriquedesc();

		$url = "";

		if($id_rubrique) {

			if ($trubrique->charger($id_rubrique)) {
				$trubriquedesc->charger($trubrique->id);
				$url = $trubriquedesc->getUrl();
			}
		}

		$racine = new Rubrique();
		$racine->charger($trubrique->id);

		while($racine->parent)
			$racine->charger($racine->parent);

		$texte = str_replace("#RUBRIQUE_CHAPO", "$trubriquedesc->chapo", $texte);
		$texte = str_replace("#RUBRIQUE_DESCRIPTION", "$trubriquedesc->description", $texte);
		$texte = str_replace("#RUBRIQUE_POSTSCRIPTUM", "$trubriquedesc->postscriptum", $texte);
		$texte = str_replace("#RUBRIQUE_ID", "$trubrique->id", $texte);
		$texte = str_replace("#RUBRIQUE_LIEN", "$trubrique->lien", $texte);
		$texte = str_replace("#RUBRIQUE_NOM", "$trubriquedesc->titre", $texte);
		$texte = str_replace("#RUBRIQUE_PARENT", "$trubrique->parent", $texte);
		$texte = str_replace("#RUBRIQUE_RACINE", "$racine->id", $texte);

		$texte = str_replace("#RUBRIQUE_REWRITEURL", $url, $texte);
		$texte = str_replace("#RUBRIQUE_URL", $url, $texte);

		return $texte;
	}

?>