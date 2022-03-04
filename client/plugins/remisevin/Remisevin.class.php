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
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
if (file_exists(SITE_DIR.'/client/plugins/remisevin/config.php')) require_once(SITE_DIR.'/client/plugins/remisevin/config.php');

class Remisevin extends PluginsClassiques {

	 	public $remise=0;
		public $nbart=0;
		public $total=0;

		function Remisevin() {
				$this->PluginsClassiques();
		}

		function aprescommande($commande) {
	  		$this->calculremisevin();
	      $_SESSION['navig']->commande->total = $_SESSION['navig']->commande->total-$this->remise;
	      $_SESSION['navig']->commande->remise = $_SESSION['navig']->commande->remise+$this->remise;
	      $commande->maj();
    }

    function totalPanier($total) {
	  		$total=1;
	      return $total;
    }

		function action() {
	      global $res;

	      // sans remise paiment avec -2
	      $this->calculremisevin(0,0,-2);

	      $res = str_replace("#REMISEVIN", $this->remise, $res);

	      $total = $_SESSION['navig']->panier->total(1,0);
				if(!empty($_SESSION['navig']->promo->valeur)) $promo = $_SESSION['navig']->promo->valeur;
				if(!empty($_SESSION['navig']->promo->type == 1)) $total -= $promo;
				elseif(!empty($_SESSION['navig']->promo->type == 2)) {
						$promo = $promo/100;
						$total -= $total*$promo;
				}

				$nbart=$_SESSION['navig']->panier->nbart();

	      $port = port();
			  if($port<0) $port=0;

	      $remise = $remise_client = $remise_promo = 0;

	  	 	if($_SESSION['navig']->client->pourcentage > 0) $remise_client = $total * $_SESSION['navig']->client->pourcentage / 100;

	  		$remise_promo += $this->calculremisevin($total);
	  		$remise = $remise_promo + $remise_client;

	      $res = str_replace("#TOTAL_REMISEVIN_PORT_PROMO", $total-($this->remise+$remise)+$port, $res);
	      $res = str_replace("#TOTAL_REMISEVIN", $total-$this->remise, $res);

	      //forcer remise paiement avec -1
	      $this->calculremisevin($total, $nbart, -1);

	      $res = str_replace("#PAIEMENT_REMISEVIN", $this->remise, $res);
	      $res = str_replace("#PAIEMENT_TOTAL_REMISEVIN_PORT_PROMO", $total-($this->remise+$remise)+$port, $res);
	      $res = str_replace("#PAIEMENT_TOTAL_REMISEVIN", $total-$this->remise, $res);
		}

		private function calculremisevin($total=0, $nbart=0, $paiement=0) {
	      if($total==0) $total = $_SESSION['navig']->panier->total(1,0);
	  		if($nbart==0) $nbart = $_SESSION['navig']->panier->nbart();
	      if($paiement==0) $paiement = $_SESSION['navig']->commande->paiement;
				if ($_SESSION['navig']->adresse != 0) {
						$adresse = new Adresse($_SESSION['navig']->adresse);
						$cpostal = $adresse->cpostal;
				} else {
						$cpostal = $_SESSION['navig']->client->cpostal;
				}
	  		$this->remise = remisevin_calcul($nbart,$total,$paiement,$cpostal);
	      $this->remise = number_format($this->remise,2,'.','');
    }

}

?>
