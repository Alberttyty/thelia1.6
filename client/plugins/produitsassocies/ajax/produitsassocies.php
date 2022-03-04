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
	require_once(__DIR__ . "/../../../../gestion/pre.php");
	require_once(__DIR__ . "/../../../../gestion/auth.php");

	require_once(__DIR__ . "/../../../../fonctions/divers.php");
  
  require_once(__DIR__ . "/../Produitsassocies.class.php");
	require_once(__DIR__ . "/../liste/produitsassocies.php");
  

?>
<?php if(! est_autorise("acces_catalogue")) exit; ?>
<?php

	header('Content-Type: text/html; charset=utf-8');

  if(isset($_GET['action'])) $action = $_GET['action'];
  else $action = '';

	switch($_GET['action']){
		case 'produit' : produitsassocies_produit(); break;
		case 'ajouter' : produitsassocies_ajouter(); break;
		case 'supprimer' : produitsassocies_supprimer(); break;
	}
?>
<?php
	function produitsassocies_produit(){
  
		$produit = new Produit();

		$query = "select * from $produit->table where rubrique=\""  . $_GET['id_rubrique'] . "\"";
		$resul = $produit->query($query);

		while($resul && $row = $produit->fetch_object($resul)){
                                         
			$produitdesc = new Produitdesc();
			$produitdesc->charger($row->id);
?>
			<option value="<?php echo $row->id; ?>"><?php echo $produitdesc->titre; ?></option>
<?php
		}
	}
?>
<?php
	function produitsassocies_ajouter(){
		$produit = new Produit();

		$produitsassocies = new Produitsassocies();
    $produitsassocies->id_objet = $_GET['id_objet'];
    $produitsassocies->type = $_GET['type'];
		$produitsassocies->id_produit = $_GET['id'];
		$produitsassocies->add();
                                    
		lister_produitsassocies($_GET['type'],$_GET['id_objet']);
	}
?>
<?php
	function produitsassocies_supprimer(){
		$produitsassocies = new Produitsassocies();
		$produitsassocies->charger($_GET['id']);
		$produitsassocies->delete();
		lister_produitsassocies($_GET['type'],$_GET['id_objet']);
	}
?>