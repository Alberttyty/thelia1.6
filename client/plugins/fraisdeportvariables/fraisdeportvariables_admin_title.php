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
autorisation("fraisdeportvariables");

preg_match("`([^\/]*).php`",$_SERVER['PHP_SELF'],$page);
if($page[1] == "commande_details"){         
?>

<link href="../client/plugins/fraisdeportvariables/css/fraisdeportvariables.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="../client/plugins/fraisdeportvariables/javascript/fraisdeportvariables.js"></script>

<?php

  
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");

  if(isset($_POST['fraisdeportvariables'])){
                                        
    $fraisdeportvariables=$_POST['fraisdeportvariables'];
    $fraisdeportvariables=str_replace(",",".",$fraisdeportvariables);
  
    if(is_numeric($fraisdeportvariables)){
                                
      $ma_commande=new Commande();
      $ma_commande->charger_ref($_GET['ref']);
      $ma_commande->port=$fraisdeportvariables; 
      $ma_commande->maj();
    
    }  
  
  }


	}
?>

