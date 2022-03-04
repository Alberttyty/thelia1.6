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
require_once __DIR__ . "/../fonctions/autoload.php";
// Définition de la navigation

class Navigation
{
		public $client;
		public $formcli;
		public $panier;
		public $urlprec;
		public $urlpageret;
		public $connecte=0;
		public $nouveau=0;
		public $paiement=0;
		public $adresse=0;
		public $commande;
		public $promo;
		public $page;
		public $lang;
		public $devise = false;
		public $formadr;

		public function __construct()
		{
				$this->panier = new Panier();
				$this->client = new Client();
				$this->formcli = new Client();
				$this->formadr = new Adresse();
				$this->commande = new Commande();
				$this->promo = new Promo();
				$this->page = 0;
				$this->lang = 1;
				$this->devise = false;
		}
}
?>
