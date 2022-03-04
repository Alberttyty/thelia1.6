<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");

class Nettoyageparam extends PluginsClassiques {

		function __construct() {
				parent::__construct("nettoyageparam");
		}

		function init() {
				$this->ajout_desc("Nettoyage paramètre GET et POST", "Nettoyage paramètre GET et POST", "", 1);
		}

		function predemarrage() {
				foreach($_REQUEST as $key => $value) {
						if(!is_array($value)) {
								$valeur=trim($value);
								$valeur=strip_tags($valeur);
								$valeur=filter_var($valeur,FILTER_SANITIZE_STRING);
								$_REQUEST[$key]=$valeur;
						}
	      }
	  }
}
?>
