<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
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
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/

?>
<div id="footerPage">&nbsp;</div>
 <!--<p class="footer"><a href="http://www.openstudio.fr" class="lien"><?php echo trad('dvp_par', 'admin'); ?> OpenStudio</a> - <a href="http://forum.thelia.net" class="lien"><?php echo trad('Forum_Thelia', 'admin'); ?></a> - <a href="http://contrib.thelia.net" class="lien"><?php echo trad('Contributions', 'admin'); ?> Thelia</a></p>-->

 <?php
 	if (class_exists('ActionsAdminModules')) ActionsAdminModules::instance()->inclure_module_admin("post");

	// Le parametre est passé par reference: utiliser un variable intermédiaire
	$tmp = "";
 	Tlog::ecrire($tmp);
 	echo $tmp;
 ?>
