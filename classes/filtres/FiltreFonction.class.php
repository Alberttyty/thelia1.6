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
/*	 Ê Êalong with this program. ÊIf not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/

require_once __DIR__ . "/../../fonctions/autoload.php";

class FiltreFonction extends FiltreBase {

	public function __construct($nom_filtre)
	{
		switch($nom_filtre)
		{
			case 'min' :
				$this->fonction = 'strtolower';
			break;
			case 'maj' :
				$this->fonction = 'strtoupper';
			break;
			case 'sanstags' :
				$this->fonction = 'strip_tags';
			break;
		}

		parent::__construct("`\#FILTRE_$nom_filtre\(([^\)]+)\)`");
	}

	public function calcule($match)
	{
		$func = $this->fonction;

		return $func($match[1]);
	}
}
?>