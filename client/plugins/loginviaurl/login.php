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
require_once(realpath(dirname(__FILE__)) . "/../../../gestion/pre.php");
include_once(realpath(dirname(__FILE__)) . "/Loginviaurl.class.php");	

session_start();
header("Content-type: text/html; charset=utf-8");

$loginviaurl = new Loginviaurl();
$loginviaurl->charger_key($_GET['key']);

if($loginviaurl->charger_key($_GET['key'])){

  $admin = new Administrateur();
  if($admin->charger_id($loginviaurl->id_admin)){
    $_SESSION["util"] = $admin;
    redirige("/gestion/".$loginviaurl->redirect);
    exit();
  }

}

redirige("/gestion/index.php");
exit();

?>