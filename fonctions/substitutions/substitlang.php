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

function substitlang($texte){

		/*
		 * roadster31: Ne sert a rien et casse l'internationalisation des PDFs dans le B.O.
		 * Un test isset($GLOBALS['dicotpl']) suffit.
		global $lang, $reptpl;

		if(! file_exists(realpath(__DIR__) . "/../../$reptpl/lang/" .  $lang . ".php"))
        	return $texte;
		*/

        if (isset($GLOBALS['dicotpl']) && is_array($GLOBALS['dicotpl'])) {

    		$cle = array_keys($GLOBALS['dicotpl'] );
	    	$valeur = array_values($GLOBALS['dicotpl'] );

	    	$count = count($cle);

	    	for($idx = 0; $idx < $count; $idx++) $cle[$idx] = "::".$cle[$idx]."::";

	   		$texte = str_replace($cle, $valeur, $texte);
    	}

		return $texte;
}

?>