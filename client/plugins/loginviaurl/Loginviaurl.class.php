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
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/divers.php");
	
	class Loginviaurl extends PluginsClassiques{
  
    var $table = "loginviaurl";
  
    var $bddvars=array("id", "login_key", "id_admin", "redirect");
  
    var $id;
    var $login_key;
    var $id_admin;
    var $redirect;

		function Loginviaurl(){
	
			$this->PluginsClassiques("loginviaurl");	
	
		}
		
		function init(){
		
      $this->ajout_desc("Login via URL", "", "", 1);
      
      $cnx = new Cnx();
  		$query = "CREATE TABLE `loginviaurl` (
  		  `id` int unsigned NOT NULL auto_increment,
  		  `login_key` varchar(255) NOT NULL,
  		  `id_admin` int unsigned NOT NULL,
        `redirect` varchar(255) NOT NULL,
  		  PRIMARY KEY  (`id`)
  		) AUTO_INCREMENT=1 ;";
  		$resul = mysql_query($query, $cnx->link);		

		}
    
    function charger_key($key){
      return $this->getVars("select * from $this->table where login_key=\"$key\"");
    }
    
    function charger_id($id){
      return $this->getVars("select * from $this->table where id=\"$id\"");
    }
    
    function charger_id_admin($id){
      return $this->getVars("select * from $this->table where id_admin=\"$id\"");
    }
    
    function genKey(){
      $this->login_key=genpass(14);
    }
		
	}

?>
