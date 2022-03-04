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
/*	 � �along with this program. �If not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/

require_once __DIR__ . "/../../fonctions/autoload.php";

class FiltreFaux extends FiltreBase {

	public function __construct()
	{
		parent::__construct("`\#FILTRE_faux\(([^\|]+)\|\|([^\)]+)\)`");
	}

	public function calcule($match)
	{
		return $match[1] == '0' ? $match[2] : '';
	}
}
?>