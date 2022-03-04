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

/* Substitutions de type commande */

function substitcommande($texte) {
	global $commande;

	if ($commande)
		$refs = $commande;
	else
		$refs = $_SESSION['navig']->commande->ref;


	$texte = str_replace("#COMMANDE_TRANSPORT", $_SESSION['navig']->commande->transport, $texte);

	$tcommande = new Commande();
	$tcommande->charger_ref($refs);

	$texte = str_replace("#COMMANDE_ID", $tcommande->id, $texte);
	$texte = str_replace("#COMMANDE_REF", $tcommande->ref, $texte);
	$texte = str_replace("#COMMANDE_TRANSACTION", $tcommande->transaction, $texte);

	return $texte;
}

?>