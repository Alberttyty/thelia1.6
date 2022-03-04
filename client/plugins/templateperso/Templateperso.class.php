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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Contenu.class.php");

class Templateperso extends PluginsClassiques {

	function __construct() {
		parent::__construct("templateperso");
	}

	function init(){
		$this->ajout_desc("Template perso", "Template perso", "", 1);
	}

	function pre()   {
		global  $reptpl,$fond,$id_rubrique,$id_produit,$id_dossier,$id_contenu;                                             
		
		//$reptpl="template/";		
		//if($fond=="index") $fond="sommaire";
		
		switch($fond) {
		
			case 'rubrique':
				if(file_exists($_SERVER{'DOCUMENT_ROOT'}."/".$reptpl."rubrique=".$id_rubrique.".html")) {
					$fond = 'rubrique='.$id_rubrique.'';
				} else {
					$Rubrique = new Rubrique();
					$Rubrique->charger($id_rubrique);
					$parent = $Rubrique->parent;
					if(file_exists($_SERVER{'DOCUMENT_ROOT'}."/".$reptpl."rubrique-".$parent.".html")) {
						$fond = 'rubrique-'.$parent.'';
					} else {
						$fond = 'rubrique';
					}
				}
				break;
		
			case 'produit':
				$produit=new Produit();
				$produit->charger_id($id_produit);
				$id_rubrique=$produit->rubrique;
				if(file_exists($_SERVER{'DOCUMENT_ROOT'}."/".$reptpl."produit=".$id_produit.".html"))  { 
					$fond="produit=".$id_produit."";
				} elseif(file_exists($_SERVER{'DOCUMENT_ROOT'}."/".$reptpl."produit-".$id_rubrique.".html")){
					$fond="produit-".$id_rubrique."";
				} else {
					$fond="produit";
				}
				break;
		
			case 'dossier':
				if(file_exists($_SERVER{'DOCUMENT_ROOT'}."/".$reptpl."dossier=".$id_dossier.".html")) { 
					$fond="dossier=".$id_dossier."";
				} else {
					$fond="dossier";
				}
				break;
		
			case 'contenu':
				$contenu=new Contenu();
				$contenu->charger($id_contenu);
				$id_dossier=$contenu->dossier;
				if(file_exists($_SERVER{'DOCUMENT_ROOT'}."/".$reptpl."contenu=".$id_contenu.".html")) { 
					$fond="contenu=".$id_contenu."";
				} elseif(file_exists($_SERVER{'DOCUMENT_ROOT'}."/".$reptpl."contenu-".$id_dossier.".html")){
					$fond="contenu-".$id_dossier."";
				} else {
					$fond="contenu";
				}
				break;
  		}
  	//echo $fond;
    //exit($reptpl);    
    }

}

?>