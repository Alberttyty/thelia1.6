<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) OpenStudio		                                             */
/*		email : thelia@openstudio.fr		        	                          	 */
/*      web : http://www.openstudio.fr						   						 */
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
/*	    along with this program.  If not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/
require_once __DIR__ . "/../../fonctions/autoload.php";

abstract class FiltreBase {
	public function __construct($regexp) {
		$this->regexp = $regexp;
	}

	public function init() {
	}

	public function prerequis() {
		return true;
	}

	public function destroy() {
	}

	public function exec(&$texte) {
		if (preg_match_all($this->regexp, $texte, $matches, PREG_SET_ORDER)) {

			foreach($matches as $match) {

				// On ne remplace que le première occurence du filtre. En effet,
				// si on a plusieurs occurences identiques du filtre, str_replace()
				// remplacera toutes ces occurences en une seule fois, ce qui n'est
				// peut-être pas le comportement adéquat.
				// Cf. http://thelia.net/forum/viewtopic.php?id=8017
            	$texte = substr_replace(
							$texte,
							$this->calcule($match),
							strpos($texte, $match[0]),
							strlen($match[0])
				);
 			}
		}
	}

	public abstract function calcule($match);
}
?>