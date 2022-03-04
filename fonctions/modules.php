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
function est_autorise($action, $type="lecture")
{
		if ($_SESSION['util']->profil == "1") return 1;

    if (isset($_SESSION['util']->autorisation[$action]) && $_SESSION['util']->autorisation[$action]->lecture)
        return 1;

    return 0;
}

/* Compatibilité < 1.5.0 */
function modules_fonction($fonc, $args = "", $nom = "")
{
		$tmp = '';
		ActionsModules::instance()->appel_module($fonc, $args, $tmp, $nom);
}
?>
