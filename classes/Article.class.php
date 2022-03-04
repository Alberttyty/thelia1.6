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
// Déniniftion de l'article
require_once __DIR__ . "/../fonctions/autoload.php";

class Article
{
		public $produit;
		public $produitdesc;
		public $quantite;
		public $parent;
		public $perso=[];

		function __construct($ref, $quantite, $perso="", $parent = -1)
		{
				$this->perso = new Perso();

				$this->produit = new Produit();
				$this->produit->charger($ref);
				$this->produitdesc = new Produitdesc();
				$this->produitdesc->charger($this->produit->id);
			  $this->quantite = $quantite;
			  $this->perso = $perso;
			  $this->parent = $parent;

				for ($i=0;$i<count($perso); $i++) {
						$declinaison = new Declinaison();
						$declinaison->charger($perso[$i]->declinaison);

						if ($declinaison->isDeclidisp()) {
								$stock = new Stock();
								$stock->charger($perso[$i]->valeur, $this->produit->id);

								if($stock->surplus != 0) {
										$this->produit->prix += $stock->surplus;
										$this->produit->prix2 += $stock->surplus;
								}
						}
				}
		}
}
?>
