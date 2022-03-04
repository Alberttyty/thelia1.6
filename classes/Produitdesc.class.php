<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
require_once __DIR__ . "/../fonctions/autoload.php";

class Produitdesc extends BaseobjdescReecriture
{
		public $id;
		public $produit;

		const TABLE = "produitdesc";
		public $table = self::TABLE;

		public $bddvars = ["id", "produit", "titre", "chapo", "description", "lang", "postscriptum"];

		public function __construct($produit = 0, $lang = false)
		{
				parent::__construct('produit', $produit, $lang);
		}

		public function charger($produit = null, $lang = null)
		{
				if ($produit != null) return $this->charger_desc($produit, $lang);
		}

		public function charger_titre($titre)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE titre='".$this->escape_string($titre)."'");
		}

		protected function clef_url_reecrite()
		{
				$produit = new Produit();
				$produit->charger_id($this->produit);

				return self::calculer_clef_url_reecrite($produit->id, $produit->rubrique);
		}

		protected function texte_url_reecrite()
		{
				$produit = new Produit();
				$produit->charger_id($this->produit);

				$rubriquedesc = new Rubriquedesc($produit->rubrique, $this->lang);

				return $produit->id . "-" . $rubriquedesc->titre . "-" . $this->titre . ".html";
		}

		public static function calculer_clef_url_reecrite($id_produit, $id_rubrique)
		{
				return "id_produit=$id_produit&id_rubrique=$id_rubrique";
		}
}
?>
