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

class Paypal extends PluginsPaiements
{
		public $defalqcmd = 0;

		function Paypal()
		{
				$this->PluginsPaiements("paypal");
		}

    function init()
		{
  			$this->ajout_desc("Paypal", "Paypal", 'Paiement crypté et sécurisé par carte bancaire.<br/><img src="/client/plugins/paypal/images/logo_paypal_us.png" alt="" class="logo_paiement" />', 1);
  	}

		function paiement($commande)
		{
				header("Location: " . "client/plugins/paypal/paiement.php");
		}

  	function confirmation($commande)
		{
  			$module = new Modules();
      	$module->charger_id($commande->paiement);
      	if($module->nom==$this->getNom()) {
	    			if ($commande->statut == 2) {
			      		parent::mail($commande);
			      		//mail('thierry@pixel-plurimedia.fr', 'Test Paypal La Bohémia', $commande->ref);
			      		//modules_fonction("statut", $commande);
	      		}
      	}
  	}
}
?>
