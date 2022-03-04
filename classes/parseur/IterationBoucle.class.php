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

require_once __DIR__ . "/../../fonctions/autoload.php";

// Définit les données resultant d'une iteration de boucle
class IterationBoucle{
	public $remplacement;
	public $prefixe;
	public $varval;

	public function __construct(){
		$this->remplacement = false;
		$this->prefixe = false;
		$this->varval = array();
	}

	public function ajoutVarVal($var, $value){
		if (trim($var) != '') $this->varval['#'.$var] = $value;
	}

	public function estValuee(){
		return count($this->varval) > 0 || $this->remplacement !== false || $this->prefixe !== false;
	}
}

?>