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

/* Substitutions de type message */
function substitmessage($texte) {

    preg_match_all("`\#MESSAGE_([^\(]+)\(([^\)]+)\)`", $texte, $cut);

    $tab1 = "";
    $tab2 = "";

    for($i=0; $i<count($cut[1]); $i++) {
		$message = new Message();
		$message->charger($cut[2][$i]);
		$messagedesc = new Messagedesc();
		$messagedesc->charger($message->id);

		if($cut[1][$i] == "TITRE") {
			$tab1[$i] = "#MESSAGE_" . $cut[1][$i] . "(" . $cut[2][$i] . ")";
	        $tab2[$i] = $messagedesc->titre;
		}
		else if($cut[1][$i] == "CHAPO") {
			$tab1[$i] = "#MESSAGE_" . $cut[1][$i] . "(" . $cut[2][$i] . ")";
	        $tab2[$i] = $messagedesc->chapo;
		}
		else if($cut[1][$i] == "DESCRIPTION") {
			$tab1[$i] = "#MESSAGE_" . $cut[1][$i] . "(" . $cut[2][$i] . ")";
	        $tab2[$i] = $messagedesc->description;
		}

    }

    $texte = str_replace($tab1, $tab2, $texte);
    return $texte;

}
?>