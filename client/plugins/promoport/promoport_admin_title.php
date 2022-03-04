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

  require_once(__DIR__ . '/../../../fonctions/authplugins.php');
  autorisation("promoport");
  
  include_once(realpath(dirname(__FILE__)) . "/Promoport.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Promo.class.php");
  
  preg_match("`([^\/]*).php`", $_SERVER['PHP_SELF'], $page);
  
  if($page[1] == "promo_modifier")
  {
?>

<link href="../client/plugins/promoport/css/promoport.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../client/plugins/promoport/javascript/promoport.js"></script>

<?php
  }
  if($page[1] == "promo")
  {
?>

<script type="text/javascript">
var promoport=[]; 
<?php
  $promoport=new Promoport();
  $promo=new Promo();
  $query = "select p.code from $promoport->table as pp,$promo->table as p where pp.id_promo=p.id";
  $resul = CacheBase::getCache()->query($query);
  foreach ($resul as $row) {
    echo 'promoport.push("'.$row->code.'");';
  }
?>
</script>

<script type="text/javascript" src="../client/plugins/promoport/javascript/promoport-liste.js"></script>

<?php
  }
?>

