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

autorisation("magasins");

include_once(realpath(dirname(__FILE__)) . "/../../../client/plugins/ckeditor/ckeditor_admin_title.php");

preg_match("`([^\/]*).php`", $_SERVER['PHP_SELF'], $page);
if(isset($_REQUEST['nom'])){$nomtest=$_REQUEST['nom'];}
else {$nomtest='';}

if(($nomtest == "magasins" && $_REQUEST['action'] == "magasins_editer")){

echo '<script language="javascript" type="text/javascript" src="../client/plugins/ckeditor/ckeditor_'.$version.'/ckeditor.js?date=020813"></script>
';

?>
<script language="javascript" type="text/javascript">
window.onload = function()
{
CKEDITOR.replace( 'description',
    {
    customConfig : '../config_produit.js'
    } );
}
</script>

<script src="http://maps.googleapis.com/maps/api/js?key=YOUR_KEY" type="text/javascript"></script>

<script language="javascript" type="text/javascript">
function getGeoLoc(){

  var geocoder = new google.maps.Geocoder();

  var address = $('#magasins_adresse').val().replace(/ /g,'+')+"+"+$('#magasins_code_postal').val().replace(/ /g,'+')+"+"+$('#magasins_ville').val().replace(/ /g,'+')+"+"+$('#magasins_pays').val().replace(/ /g,'+');

  geocoder.geocode( { 'address': address}, function(results, status) {
  if (status == google.maps.GeocoderStatus.OK) {
    $('#magasins_lat').val(results[0].geometry.location.lat());
    $('#magasins_lng').val(results[0].geometry.location.lng());
  } else {
    alert('Geocode was not successful for the following reason: ' + status);
  }
  });

}
</script>

<?php
	}
?>
