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
?>
<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
	
	
	class Prixht extends PluginsClassiques{
		
		function Prixht(){
			$this->PluginsClassiques("Prixht");	
		}

		function init(){
		
		  $this->ajout_desc("Prix ht", "Saisie des prix ht", "", 1);
							
		}
    
    function ajoutprod($produit){
		
    if (isset($_POST['prixht'])) $prixht = $_POST['prixht'];
    if (isset($_POST['prix2ht'])) $prix2ht = $_POST['prix2ht'];
    
    if($prixht!=0)
    $produit->prix=$prixht*(1+($produit->tva/100));
    if($prix2ht!=0)
    $produit->prix2=$prix2ht*(1+($produit->tva/100));
    
    $produit->maj();
    	
		}
    
    function modprod($produit){
		
    if (isset($_POST['prixht'])) $prixht = $_POST['prixht'];
    if (isset($_POST['prix2ht'])) $prix2ht = $_POST['prix2ht'];
    
    if($prixht!=0)
    $produit->prix=$prixht*(1+($produit->tva/100));
    if($prix2ht!=0)
    $produit->prix2=$prix2ht*(1+($produit->tva/100));
    
    $produit->maj();
    	
		}
		
	}


?>
