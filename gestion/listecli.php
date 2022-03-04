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
require_once("pre.php");
require_once("auth.php");
if(! est_autorise("acces_clients")) exit; 

$client = new Client();
$querystring = $_GET['queryString'];
$query = "select * from $client->table where nom like '$querystring%' or prenom like '$querystring%' or entreprise like '$querystring%'";
$resul = mysql_query($query,$client->link);

while($row = mysql_fetch_object($resul)){
    ?>
    <li onclick="fill('<?php echo $row->ref."|".$row->nom." ".$row->prenom; ?>')" style="left:200px;"><?php echo $row->nom." ".$row->prenom; ?></li>
    <?php
}
?>

