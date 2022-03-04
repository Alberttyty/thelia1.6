<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) CQFDev					                                     */
/*		email : contact@cqfdev.fr		        	                             	 */
/*      web : http://www.cqfdev.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
require_once(dirname(realpath(__FILE__)) . '"/../../../classes/filtres/FiltreBase.class.php');

class Filtrecalcul extends FiltreBase {

	public function __construct() {
		parent::__construct("`\#FILTRE_calcul\("
    		."[\s]*([^,]+)[\s]*,"
    		."[\s]*([^,]+)[\s]*,"
    		."[\s]*([^,]+)[\s]*"
    		."(?:,[\s]*([0-9]+))*[\s]*"
    		."(?:,[\s]*[\"']([^\"']+)[\"']){0,1}[\s]*"
    		."(?:,[\s]*[\"']([^\"']+)[\"']){0,1}[\s]*"
    		."\)`");
	}

	public function calcule($match)	{
		$operande1 = $match[1];
		$operateur = $match[2];
		$operande2 = $match[3];

      	switch ($operateur) {
			case '+':
				$resultat = $operande1 + $operande2;
				break;

			case '-':
				$resultat = $operande1 - $operande2;
				break;

			case '*':
				$resultat = $operande1 * $operande2;
				break;

			case '/':
				$resultat = $operande1 / $operande2;
				break;

			case 'mod':
				$resultat = $operande1 % $operande2;
				break;

			default:
				break;
	    }

	    if (isset($match[4])) {
	    	$ds = isset($match[5]) ? $match[5] : '.';
	    	$ts = isset($match[6]) ? $match[6] : '';

	    	$resultat = number_format($resultat, intval($match[4]), $ds, $ts);
	    }

		return $resultat;
	}
}
