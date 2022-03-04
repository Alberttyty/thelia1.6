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

class Produit extends Baseobjclassable
{
		public $id;
		public $ref;
		public $datemodif;
		public $prix;
		public $ecotaxe;
		public $promo;
		public $ligne;
		public $garantie;
		public $prix2;
		public $rubrique;
		public $nouveaute;
		public $perso;
		public $stock;
		public $poids;
		public $tva;
		public $classement;

		const TABLE="produit";
		public $table=self::TABLE;

		public $bddvars=array("id", "ref", "datemodif", "prix", "ecotaxe", "promo", "ligne", "garantie", "prix2", "rubrique", "nouveaute", "perso", "stock", "poids", "tva", "classement");

		public function __construct($ref = "")
		{
				parent::__construct("rubrique", "modprod");
				if ($ref != "") $this->charger($ref);
		}

		public function charger($ref = null, $var2 = null)
		{
				if ($ref != null) return $this->getVars("SELECT * FROM $this->table WHERE ref=\"$ref\"");
		}

		public function delete()
		{
				if (! empty($this->id)) {
						$this->delete_cascade('Image', 'produit', $this->id);
						$this->delete_cascade('Document', 'produit', $this->id);
						$this->delete_cascade('Stock', 'produit', $this->id);
						$this->delete_cascade('Accessoire', 'produit', $this->id);
						$this->delete_cascade('Accessoire', 'accessoire', $this->id);
						$this->delete_cascade('Caracval', 'produit', $this->id);
						$this->delete_cascade('Exdecprod', 'produit', $this->id);
						$this->delete_cascade('Produitdesc', 'produit', $this->id);

						parent::delete();
				}
		}
}
?>
