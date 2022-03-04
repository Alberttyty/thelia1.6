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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsPaiements.class.php");

class Paiementce extends PluginsPaiements {

    public $defalqcmd = 1;

		function __construct() {
				parent::__construct("paiementce");
		}

		function init() {
				$this->ajout_desc("Paiement pour CE", "Paiement pour CE", "", 1);
		}

		function paiement($commande) {
	      $module = new Modules();
	      $module->charger_id($commande->paiement);
	      if ($module->nom==$this->getNom()){
	  		  	$commande->setStatutAndSave(2);
	        	$commande->setStatutAndSave(1);
	      }
	      header("Location: " . urlfond("paiementce"));
	   		exit();
		}

}

?>
