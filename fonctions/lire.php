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
require_once(__DIR__ . "/../lib/TheliaPurifier.php");

/**
 * deprecated depuis thelia 1.5.3.5
 *
 * @param type $var nom du param fond
 * @param type $varFond inutilisé
 */
function lireVarFond($var, $varFond=null)
{
    Parseur::lireVarFond($var, '');
}

function lireVarUrl($var, $varUrl)
{
    if (preg_match("/&$var=([a-zA-Z0-9_\-\%\+]*)/", $varUrl, $rec)) return $rec[1];
    else return "";
}

// lecture des arguments
function lireTag($ligne, $tag, $filtre = "", $purifier = 1)
{
  	preg_match("/$tag=\"([^\"]*)\"/", "$ligne", $restag);

  	if (empty($restag)) return "";

    if (preg_match("/^([^\+]*)\+(.*)$/", $filtre, $resfiltre)) {
    		$filtre = $resfiltre[1];
    		$complement = $resfiltre[2];
  	}
    else $complement = '';

	  return filtrevar($restag[1], $filtre, $complement, $purifier);
}

function lireParam($param, $filtre="", $methode="", $purifier = 1)
{
  	if ($methode == "post") $tab = &$_POST;
  	else if($methode == "get") $tab = &$_GET;
  	else $tab = &$_REQUEST;

  	if (isset($tab[$param])) {
    		$param = $tab[$param];

    		//if (get_magic_quotes_gpc()) $param = stripslashes($param);

        if (preg_match("/^([^\+]*)\+(.*)$/", $filtre, $resfiltre)) {
      			$filtre = $resfiltre[1];
      			$complement = $resfiltre[2];
    		}
        else $complement = "";

    		return filtrevar($param, $filtre, $complement, $purifier);
  	}
    else return '';
}

function filtrevar($var, $filtre, $complement="", $purifier = 1)
{
  	$erreur = 0;
    
  	if ($filtre == "" || $var == "")	return $var;

  	switch($filtre) {
    		case "int" : if(! preg_match("/^[0-9$complement]*$/", $var)) $erreur = 1; break;
    		case "string_iso_strict" : if(! preg_match("/^[0-9a-zA-Z_]*$/", $var)) $erreur = 1; break;
    		case "string": if($purifier) $var = TheliaPurifier::instance()->purifier($var); break;
    		case "float" : if(! preg_match("/^[0-9\.\,$complement]*$/", $var)) $erreur = 1; break;
    		case "int_list": if(! preg_match("/^[0-9\,$complement]*$/", $var)) $erreur = 1; break;
    		case "string_list":	if($purifier) $var = TheliaPurifier::instance()->purifier($var); break;
    		default: break;
  	}

  	if ($erreur == 1) return '';

  	// Pour les boucles
  	$var = str_replace("\"", "&quot;", $var);
  	return $var;
}
?>
