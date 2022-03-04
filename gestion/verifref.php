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
require_once("pre.php");
require_once("auth.php");
?>
<?php if(! est_autorise("acces_catalogue")) exit; ?>
<?php

$produit = new Produit();
if($produit->charger($ref)){
	$proddesc = new Produitdesc();
	$proddesc->charger($produit->id);
	$prix = $produit->prix;
	if($produit->promo) $prix = $produit->prix2;
	echo $proddesc->titre."|".$prix."|".$produit->tva;
}
else{
	echo ""."|".""."|"."";
}

?>