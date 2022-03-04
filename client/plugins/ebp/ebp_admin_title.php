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
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("ebp");

preg_match("`([^\/]*).php`",$_SERVER['PHP_SELF'],$page);
if(($page[1] == "module" && $_GET['nom']="ebp")||$page[1] == "accueil"){         
?>

<link href="../client/plugins/ebp/css/ebp.css?version=3" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="../client/plugins/ebp/javascript/ebp.js"></script>

<?php
	}
  if($page[1] == "rubrique_modifier"){         
?>

<script language="javascript" type="text/javascript" src="../client/plugins/ebp/javascript/ebp_rubrique.js"></script>

<?php
	}
?>
