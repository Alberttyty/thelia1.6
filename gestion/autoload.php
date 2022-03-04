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

    require_once __DIR__ . '/../classes/Autoload.class.php';
    
    $basedir = __DIR__ . "/../";

    $autoload = Autoload::getInstance();

    $autoload->addDirectories(array(
        $basedir . "/classes/",
        $basedir . "/classes/actions/",
        $basedir . "/classes/filtres/",
        $basedir . "/classes/parseur/",
        $basedir . "/classes/tlog/",
        $basedir . "/classes/tlog/destinations",
        __DIR__ . "/classes/",
        __DIR__ . "/actions/",
      ));

    $autoload->register();
?>
