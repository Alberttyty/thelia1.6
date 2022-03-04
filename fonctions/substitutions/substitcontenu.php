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

	/* Substitutions de type contenu */

	function substitcontenu($texte){
		global $motcle, $id_contenu;

		$tcontenu = new Contenu();
		$tcontenudesc = new Contenudesc();
		$url = "";

		if($id_contenu){
			if ($tcontenu->charger($id_contenu)) {
				$tcontenudesc->charger($tcontenu->id);
				$url = $tcontenudesc->getUrl();
			}
		}

		$texte = str_replace("#CONTENU_URL", $url, $texte);

		$texte = str_replace("#CONTENU_ID", "$id_contenu", $texte);
		$texte = str_replace("#CONTENU_MOTCLE", "$motcle", $texte);
		$texte = str_replace("#CONTENU_NOM", $tcontenudesc->titre, $texte);
		$texte = str_replace("#CONTENU_CHAPO", $tcontenudesc->chapo, $texte);
		$texte = str_replace("#CONTENU_DESCRIPTION", $tcontenudesc->description, $texte);
		$texte = str_replace("#CONTENU_POSTSCRIPTUM", $tcontenudesc->postscriptum, $texte);

		return $texte;

	}
?>