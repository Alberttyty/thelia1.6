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

	class Affiliation extends PluginsClassiques{
  
    var $listeSites;

		function __construct(){
			parent::__construct("affiliation");
      $this->listeSites=array(
      "0001" => "Babel Voyages"
      );
		}

		function init(){
			$this->ajout_desc("Affiliation", "Affiliation", "", 1);
		}

		function demarrage() {
      if(isset($_GET['ecm_origine'])){
      
        setcookie('ecm_origine',$_GET['ecm_origine'], time() + 5*24*3600, null, null, false, true);
      
      }
    }
    
    function post(){

      global $res;
      
      $valeur="";
      
      if(isset($_COOKIE['ecm_origine'])){
        if($_COOKIE['ecm_origine']!=""){
          $valeur=$this->listeSites[$_COOKIE['ecm_origine']];
        }
      }
    
      $res = str_replace("#SITEPROVENANCE", $valeur, $res);
      
    
    }

	}

?>
