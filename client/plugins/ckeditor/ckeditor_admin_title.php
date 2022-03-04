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

autorisation("ckeditor");

$version="4.5.1";

preg_match("`([^\/]*).php`", $_SERVER['PHP_SELF'], $page);
if(isset($_REQUEST['nom'])){$nomtest=$_REQUEST['nom'];}
else {$nomtest='';}

if($page[1] == "rubrique_modifier" || $page[1] == "produit_modifier" || $page[1] == "contenu_modifier" || ($nomtest == "agenda" && $_REQUEST['action'] == "visualiser") || ($nomtest == "revuedepresse" && $_REQUEST['action'] == "visualiser")){

/*include_once("../classes/Variable.class.php");
$style_chem = new Variable();
$style_chem->charger("style_chem");*/

echo '<script language="javascript" type="text/javascript" src="../client/plugins/ckeditor/ckeditor_'.$version.'/ckeditor.js?date=020813"></script>
';

?>
<script language="javascript" type="text/javascript">
window.onload = function()
{
CKEDITOR.replace( 'description',
    {
    
    <?php
    echo "contentsCss : ['".FICHIER_URL."template/css/initialisation.css','".FICHIER_URL."template/css/texte.css','".FICHIER_URL."template/css/bouton.css'],"
    ?>
    
	<?php
    if($page[1] == "contenu_modifier")
    {
    ?>
	 customConfig : '../config_contenu.js'
	<?php
    }
    else
    {
    ?>
	 customConfig : '../config_produit.js'
	<?php
    }
    ?>
	 
    } );
}
</script>

<?php
	}
?>

