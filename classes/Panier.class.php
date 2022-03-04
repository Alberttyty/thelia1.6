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
require_once(__DIR__ . "/../fonctions/divers.php");

// Déninition du panier
class Panier
{
		public $nbart;
		public $tabarticle;

		function __construct()
		{
				$this->nbart = 0;
				$this->tabarticle=[];
		}

		function ajouter($ref, $quantite, $tdeclidisp=[], $append=0, $nouveau=0, $parent=-1)
		{
				$indiceAjoute = false;

				if ($this->verfistock($ref, $quantite, $tdeclidisp)) {
						$existe = false;

						foreach($this->tabarticle as $index => $art) {
								// L'article est dans le panier ?
								if (isset($art->produit->ref) && $art->produit->ref == $ref && $art->parent === $parent) {
										$existe = true;
										$indice = $index;

										if (! (empty($tdeclidisp) && empty($art->perso)) ) {
												if ($art->perso != $tdeclidisp) $existe = false;
										}
										else if (intval($art->produit->stock) < ($art->quantite + $quantite)) {
												$append = 0;
										}

										if ($existe) break;
								}
						}

						if (! $existe || $nouveau == 1) {
								$indiceAjoute = $this->nbart;
								$this->tabarticle[] = new Article($ref, $quantite, $tdeclidisp, $parent);
								$this->nbart++;
						}
						else if ($existe && $append) {
								$indiceAjoute = $indice;
								$this->tabarticle[$indice]->quantite += $quantite;
						}
				}

				ActionsModules::instance()->appel_module("ajouterPanier", $indiceAjoute);

				return $indiceAjoute;
		}

		function supprimer($id)
		{
				if (isset($this->tabarticle[$id])) {
						// Supprimer l'élément concerné
						unset($this->tabarticle[$id]);

						// Restaurer la continuité des indexes
						$this->tabarticle = array_values($this->tabarticle);
						$this->nbart--;

						$listeFils = [];
						for($i=0; $i<$this->nbart; $i++) {
								if($this->tabarticle[$i]->parent > $id) $this->tabarticle[$i]->parent--;
								elseif($this->tabarticle[$i]->parent==$id) $listeFils[] = $i;
						}

						for($i=0; $i<count($listeFils); $i++) $this->supprimer($listeFils[$i]);
				}
		}

		function modifier($article, $quantite, $parent=-1)
		{
				$art = &$this->tabarticle[$article];
				// Vérification du stock
				if (! $this->verfistock($art->produit->ref, $quantite, $art->perso)) return 0;

				$art->quantite = $quantite;

				if($parent>-2 && isset($this->tabarticle[$parent])) $art->parent = $parent;
		}


		function total($tva=1, $remise=0)
		{
				$total = 0;
				$taxe = 0;

				$pays = new Pays();

				if($_SESSION['navig']->adresse != "" && $_SESSION['navig']->adresse != 0) {
						$adr = new Adresse();
						$adr->charger($_SESSION['navig']->adresse);
						$pays->charger($adr->pays);
				}
				else $pays->charger($_SESSION['navig']->client->pays);

				foreach($this->tabarticle as $art) {
						$prix = $art->produit->promo ? $art->produit->prix2 : $art->produit->prix;
						$taxe += ($prix - ($prix/(1+$art->produit->tva/100))) * $art->quantite;
						$total += $prix*$art->quantite;
				}

				if($tva && $pays->tva != "" && ! $pays->tva) $total -= $taxe;
				else if($tva == 0) $total -= $taxe;

				$total -= $remise;

				ActionsModules::instance()->appel_module("totalPanier", $total);

				return round($total, 2);
		}

		function totalecotaxe()
		{
				$ecotaxe = 0;
				foreach($this->tabarticle as $art) {
						$ecotaxe += $art->produit->ecotaxe * $art->quantite;
				}
				return round($ecotaxe, 2);
		}

		function totaltva()
		{
				return $this->total() - $this->total(0);
		}

		function poids()
		{
				$poids = 0;
				foreach($this->tabarticle as $art) {
						$poids += $art->produit->poids * $art->quantite;
				}
				return round($poids, 2);
		}

		function nbart()
		{
				$nbart = 0;
				foreach($this->tabarticle as $art) {
						$nbart += $art->quantite;
				}
				return $nbart;
		}

		function unitetr()
		{
				$unitetr = 0;
				foreach($this->tabarticle as $art) {
						$unitetr += $art->produit->unitetr * $art->quantite;
				}
				return round($unitetr, 2);
		}

		function recupArticles()
		{
				return $tabarticle;
		}

		function verfistock($refproduit, $quantite, $perso)
		{
				$stockok = true;

				if (Variable::lire("verifstock", 0) == 1) {
						$prod = new Produit();

						if ($prod->charger($refproduit)) {
								if ($prod->stock >= $quantite) {
										foreach($perso as $decli) {
												$stock = new Stock();
												if ($stock->charger($decli->valeur, $prod->id) && $stock->valeur < $quantite) {
														$stockok = false;
														break;
												}
										}
								}
								else $stockok = false;
						}
						else $stockok = false;
				}

				$parametres = [
						"refproduit" => $refproduit,
						"quantite" =>$quantite,
						"perso" => $perso
				];

		    ActionsModules::instance()->appel_module(
		        "apresverifstock",
		        $stockok,
		        $parametres
		    );

				return $stockok;
		}
}
?>
