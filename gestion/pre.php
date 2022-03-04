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
if (strstr( $_SERVER['PHP_SELF'], "/admin/")) {
    header("Location: changerep.php"); exit;
}

define('THELIA_MAGIC_QUOTE_ENABLED', false);

require_once(__DIR__ . "/../fonctions/error_reporting.php");
require_once( __DIR__ . "/../fonctions/mutualisation.php");
require_once(__DIR__ . "/autoload.php");
require_once(__DIR__ . "/../lib/TheliaPurifier.php");

function _sanitize_param($value, $config = null) {
    if (is_array($value)) {
        foreach($value as $key => $item) {
            $value[$key] = _sanitize_param($item, $config);
        }
        return $value;
    }
    else {
        if(THELIA_MAGIC_QUOTE_ENABLED) $value = stripcslashes($value);
        return TheliaPurifier::instance()->purifier($value);
    }
}

ActionsLang::instance()->set_mode_backoffice(true);

Tlog::mode_back_office(true);
require_once(__DIR__ . "/../fonctions/divers.php");

// Put sanitize_admin value to 0 into variable table if you don't want to sanitize (escaping) $_REQUEST parameters
TheliaPurifier::instance()->set_admin_mode();

foreach ($_REQUEST as $key => $value) $$key = Variable::lire('sanitize_admin',1) ? _sanitize_param($value) : $value;

?>
