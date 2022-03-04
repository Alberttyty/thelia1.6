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

/**
 * Vérification de l'existence d'une url
 * @param string url l'URL à vérifier
 * @return boolean true si l'URL existe et est lisible, false sinon.
 */
function url_exists($url) {
	if ($handle = @fopen($url, "r")) {
		fclose($handle);
		return true;
	}

	return false;
}

/**
 * Redirige vers une URL, et termine le script
 * @param string url l'URL de destination
 */
function redirige($url) {
	ActionsModules::instance()->appel_module("redirige", $url);
	if (! Tlog::instance()->afficher_redirections($url)) header("Location: " . $url);
	exit;
}

/**
 *
 * Retourne l'URL du site en fonction de la configuration et de la langue courante, ou de la langue indiquée
 * @param int $idlang ID de la langue dont on cherche l'URL
 * @return l'URL du iste pour la langue donnée.
 */
function urlsite($idlang = false) {

	if ($idlang === false) $lang = new Lang(ActionsLang::instance()->get_id_langue_courante());
	else $lang = new Lang($idlang);

	if (empty($lang->url) || (Variable::lire('un_domaine_par_langue') == 0)) return  Variable::lire('urlsite');
	else return $lang->url;
}

/**
 * Calculer une URL à partir d'un nom de fond.
 *
 * @param string fond le nom du fond
 * @param string parametres les parametres éventuels à ajouter à l'URL
 * @param boolean escape si true, on sépoare l'URL et les paramètres par &amp;. Si false, par '&'
 */
function urlfond($fond="", $parametres = false, $escape = false) {

	$urlsite = urlsite();

	if (! empty($fond)) {


		if (Variable::lire("rewrite") != 0) {
			// Trouver une éventuelle reecriture
			$rw = new Reecriture();

			if ($rw->charger_param($fond, $parametres == false ? '' : "&".$parametres , ActionsLang::instance()->get_id_langue_courante(), 1)) {
				return "$urlsite/$rw->url";
			}
		}

		$urlsite = sprintf("%s/?fond=%s", $urlsite, $fond);
	}

	if ($parametres !== false) {
		if ($escape) $parametres = escape_ampersand($parametres);
		$urlsite .= ($escape ? "&amp;" : "&") . $parametres;
	}

	return $urlsite;
}

/**
 * Supprime le paramètre "déconnexion" d'une URL
 *
 * @param string url l'url a traiter
 * @return string l'URL sans le paramètre déconnexion
 */

function supprimer_deconnexion($url) {

	// Supprimer l'éventuel paramètre de déconnexion, en, préservant les éventuels autres.
	$url = preg_replace("/\?action=deconnexion\&(.+)$/", "?\\1", $url);
	$url = preg_replace("/(\?action=deconnexion$)|((\&)*action=deconnexion)/", "", $url);

	return $url;
}

/*
 * Permet de transformer les "&" d'une URL en &amp; en évitant les double encodages
 *
 * @param string url l'url ou la fraction d'url a traiter
 * @return string l'URL avec les & transformés en &amp;
 */
function escape_ampersand($url) {

	// Pas de double encodage
	$url = str_replace("&amp;", "&", $url);

	return str_replace("&", "&amp;", $url);
}

/**
 * @return string l'URL complète de la page courante. Si le rewriting est activé, il
 * s'agit de l'URL ré-écrite.
 */
function url_page_courante() {
	$uri = $_SERVER['REQUEST_URI'];

	// Si on est a la racine, s'assurer qu'on a une url avec fichier
	if (substr($uri, -1) == '/') $uri = $uri . 'index.php';

	// On utilise ensuite basename() pour retirer l'éventuel chemin comme dans http://www.maboutique.com/path/to/maboutique/mapage.html
	return urlfond() . '/' . basename($uri);
}
?>