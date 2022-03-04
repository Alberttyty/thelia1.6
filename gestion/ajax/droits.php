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
    require_once(__DIR__ . "/../pre.php");
    require_once(__DIR__ . "/../auth.php");

    require_once(__DIR__ . "/../../fonctions/divers.php");
    
    header('Content-Type: text/html; charset=utf-8');
	
  if(! est_autorise("acces_configuration")) exit; 

  if($_GET['type_droit'] == "1"){
	$autorisation = new Autorisation();
	$autorisation->charger_id($_GET['autorisation']);
	$autorisation_administrateur = new Autorisation_administrateur();
	$autorisation_administrateur->charger($autorisation->id, $_GET['administrateur']);

	if($autorisation->type == "1"){

		if($_GET['valeur'] == 1){
			$autorisation_administrateur->lecture = 1;
			$autorisation_administrateur->ecriture = 1;
		} else {
			$autorisation_administrateur->lecture = 0;
			$autorisation_administrateur->ecriture = 0;
		}
		
	} else if($autorisation->type == "2"){
		
		
	}


	if($autorisation_administrateur->id)
		$autorisation_administrateur->maj();
	else {
		$autorisation_administrateur->autorisation = $autorisation->id;
		$autorisation_administrateur->administrateur = $_GET['administrateur'];
		$autorisation_administrateur->add();
	}
 } else if($_GET['type_droit'] == "2"){
	
		$autorisation_modules = new Autorisation_modules();
		$autorisation_modules->charger($_GET['module'], $_GET['administrateur']);
		
		if($_GET['valeur'] == 1)
			$autorisation_modules->autorise = 1;
	 	else 
			$autorisation_modules->autorise = 0;

		if($autorisation_modules->id)
			$autorisation_modules->maj();
		else {
			$autorisation_modules->module = $_GET['module'];
			$autorisation_modules->administrateur = $_GET['administrateur'];
			$autorisation_modules->add();
		}	
 }
?>