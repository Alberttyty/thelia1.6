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

class Mutualisation extends PluginsClassiques {

		function Mutualisation() {
				$this->PluginsClassiques();
		}

 	 	function inclusion() {
    		/*global $res,$reptpl;
    		$res=str_replace('#INCLURE "template/','#INCLURE "'.$reptpl,$res);*/
  	}

  	function analyse() {
    		global $res,$reptpl;
    		$res=str_replace('#DOSSIER_TEMPLATE',rtrim($reptpl,'/'),$res);
  	}

  	function modcont($contenu) {
    		global $lang;
    		$contenudesc = new Contenudesc();
				$contenudesc->charger($contenu->id,$lang);
				$contenudesc->description=preg_replace('/src=("|\'){1}\/client\/(.+)("|\'){1}/','src="'.FICHIER_URL.'client/$2"',$contenudesc->description);
				$contenudesc->maj();
				/*$query = "
						select id from
							$contenu->table
						where
							1";
				$resul = CacheBase::getCache()->query($query);
				foreach ($resul as $row) {
				  	$contenudesc = new Contenudesc();
				  	$contenudesc->charger($row->id,$lang);
				  	$contenudesc->description=str_replace("wunsch-mann.pixel-plurimedia.fr", "wunsch-mann.fr",$contenudesc->description);
				  	$contenudesc->maj();
				} */
  	}

  	function modprod($produit) {
				global $lang;
				$produitdesc = new Contenudesc();
				$produitdesc->charger($produit->id,$lang);
				$produitdesc->description=preg_replace('/src=("|\'){1}\/client\/(.+)("|\'){1}/','src="'.FICHIER_URL.'client/$2"',$produitdesc->description);
				$produitdesc->maj();
  	}

  	function modrub($rubrique) {
				global $lang;
				$rubriquedesc = new Contenudesc();
				$rubriquedesc->charger($rubrique->id,$lang);
				$rubriquedesc->description=preg_replace('/src=("|\'){1}\/client\/(.+)("|\'){1}/','src="'.FICHIER_URL.'client/$2"',$rubriquedesc->description);
				$rubriquedesc->maj();
 		}

}
?>
