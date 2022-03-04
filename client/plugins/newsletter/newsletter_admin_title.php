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

autorisation("newsletter");

preg_match("`([^\/]*).php`", $_SERVER['PHP_SELF'], $page);
if(isset($_REQUEST['nom'])){$nomtest=$_REQUEST['nom'];}
else {$nomtest='';}

if(($nomtest == "newsletter" && $_REQUEST['action_newsletter'] == "campagne_editer")){

  include_once(realpath(dirname(__FILE__)) . "/Newsletter.class.php");

	$campagne = new Newsletter_campagne();

  if(!$_REQUEST['id']) {

    if(file_exists("../template/css/newsletter.css"))
  	{
    $css='/template/css/newsletter.css';
    }
    else
    {
    $css='/client/plugins/newsletter/template/css/newsletter.css';
    }
    
  }
  else {
    
    $css='/client/plugins/newsletter/css.php?id='.$_REQUEST['id'];
    
  }

?>
<script language="javascript" type="text/javascript" src="../client/plugins/ckeditor/ckeditor/ckeditor.js"></script>
<script language="javascript" type="text/javascript">
window.onload = function()
{
CKEDITOR.replace( 'description',
    {
	<?php
    if ($nomtest == "newsletter" && $_REQUEST['action_newsletter'] == "campagne_editer")
    {
  ?>
   customConfig : '../config_newsletter.js',
   contentsCss : '<?php echo $css; ?>'
  <?php
    }
  ?>	 
    } );
}
</script>

<?php
	}
?>

