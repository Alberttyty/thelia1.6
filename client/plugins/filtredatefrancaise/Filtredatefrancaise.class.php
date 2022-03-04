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
require_once(dirname(realpath(__FILE__)) . '/../../../classes/filtres/FiltreBase.class.php');

class Filtredatefrancaise extends FiltreBase {

		public function __construct() {
  			parent::__construct("`\#FILTRE_datefrancaise\(([^\)]*)\)`");
  	}

    public function calcule($match) {
      	$tab2="";

      	if($match[1]=="") return "";

      	$date=explode("-",$match[1]);
      	$tab2=$date[2]."/".$this->getMois($date[1])."/".substr($date[0],2);

      	return $tab2;
  	}

		private function getMois($mois) {

      	$lang=1;
      	$nomsmois=array();

      	if($lang==1) {
						/*$nomsmois['01']='Jan.';
						$nomsmois['02']='Fév.';
						$nomsmois['03']='Mars';
						$nomsmois['04']='Avr.';
						$nomsmois['05']='Mai';
						$nomsmois['06']='Juin';
						$nomsmois['07']='Juil.';
						$nomsmois['08']='Août';
						$nomsmois['09']='Sept.';
						$nomsmois['10']='Oct.';
						$nomsmois['11']='Nov.';
						$nomsmois['12']='Déc.';*/

						$nomsmois['01']='01';
						$nomsmois['02']='02';
						$nomsmois['03']='03';
						$nomsmois['04']='04';
						$nomsmois['05']='05';
						$nomsmois['06']='06';
						$nomsmois['07']='07';
						$nomsmois['08']='08';
						$nomsmois['09']='09';
						$nomsmois['10']='10';
						$nomsmois['11']='11';
						$nomsmois['12']='12';
      	}

      	return $nomsmois[$mois];
    }

}

?>
