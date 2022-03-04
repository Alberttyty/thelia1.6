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

	/* Substitutionss de type panier */

	function substitpanier($texte){

		$total = 0;
        $totalht = 0;
        $totaleco = 0;
        $totaltaxe = 0;

		$allItemCount = $nb_article = 0;

		if($_SESSION['navig']->adresse){
			$adr = new Adresse();
			$adr->charger($_SESSION['navig']->adresse);
			$idpays = $adr->pays;
		} else {
			$idpays = $_SESSION['navig']->client->pays;
		}

		$pays = new Pays();
		$pays->charger($idpays);

		$total = $_SESSION['navig']->panier->total();
		$totalht = $_SESSION['navig']->panier->total(0);
		$totaleco = $_SESSION['navig']->panier->totalecotaxe();
		$tva = $total - $totalht;
		$nb_article = $_SESSION['navig']->panier->nbart;

		foreach($_SESSION['navig']->panier->tabarticle as $anItem){
			$allItemCount += $anItem->quantite;
		}
		unset($anItem);

		$port = port();
		if($port<0)
			$port = 0;

		$totcmdport = $total + $port;

		$remise = $remise_client = $remise_promo = 0;

	 	if($_SESSION['navig']->client->pourcentage>0) $remise_client = $total * $_SESSION['navig']->client->pourcentage / 100;

		$remise_promo += calc_remise($total);

		$remise = $remise_promo + $remise_client;

        $totcmdport -= $remise;
		$totremise = $total-$remise;

	    if($totcmdport<$port)
		    $totcmdport = $port;

        $totcmdportht = $totalht+$port;


		$totalht = formatter_somme($totalht);
		$total = formatter_somme($total);
		$totaleco = formatter_somme($totaleco);
		$totaltaxe = formatter_somme($totaltaxe);
		$port = formatter_somme($port);
		$totcmdport = formatter_somme($totcmdport);
		$remise = formatter_somme($remise);
		$totremise = formatter_somme($totremise);
                $totcmdportht = formatter_somme($totcmdportht);
		$tva = formatter_somme($tva,2,".","");
                $remise_client = formatter_somme($remise_client);
                $remise_promo = formatter_somme($remise_promo);

		$totpoids = $_SESSION['navig']->panier->poids();

		$texte = str_replace("#PANIER_TOTALHT", "$totalht", $texte);
		$texte = str_replace("#PANIER_TOTALECO","$totaleco",$texte);
		$texte = str_replace("#PANIER_TOTALTVA","$tva",$texte); // total TVA du panier = #PANIER_TVA
		$texte = str_replace("#PANIER_TOTAL", "$total", $texte);
		$texte = str_replace("#PANIER_PORT", "$port", $texte);
        $texte = str_replace("#PANIER_TOTPORTHT", "$totcmdportht", $texte);
		$texte = str_replace("#PANIER_TOTPORT", "$totcmdport", $texte);
		$texte = str_replace("#PANIER_TOTREMISE","$totremise",$texte);
		$texte = str_replace("#PANIER_REMISE_CLIENT", "$remise_client", $texte);
		$texte = str_replace("#PANIER_REMISE_PROMO", "$remise_promo", $texte);
		$texte = str_replace("#PANIER_REMISE", "$remise", $texte);
		$texte = str_replace("#PANIER_NBART_TOTAL", $allItemCount, $texte);
		$texte = str_replace("#PANIER_NBART", "" . $nb_article . "", $texte);
		$texte = str_replace("#PANIER_POIDS", "$totpoids", $texte);
		$texte = str_replace("#PANIER_TVA","$tva",$texte); // total TVA du panier

		return $texte;

	}
?>
