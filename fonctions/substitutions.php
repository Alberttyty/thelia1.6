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

	/* Subsitutions simples */
	require_once(__DIR__ . "/substitutions/substitrubriques.php");
	require_once(__DIR__ . "/substitutions/substitproduits.php");
	require_once(__DIR__ . "/substitutions/substitpanier.php");
	require_once(__DIR__ . "/substitutions/substitclient.php");
	require_once(__DIR__ . "/substitutions/substitpage.php");
	require_once(__DIR__ . "/substitutions/substitadresse.php");
	require_once(__DIR__ . "/substitutions/substitcommande.php");
	require_once(__DIR__ . "/substitutions/substitmessage.php");
	require_once(__DIR__ . "/substitutions/substitvariable.php");
	require_once(__DIR__ . "/substitutions/substitcaracteristique.php");
	require_once(__DIR__ . "/substitutions/substitdeclinaison.php");
	require_once(__DIR__ . "/substitutions/substitimage.php");
	require_once(__DIR__ . "/substitutions/substitdossier.php");
	require_once(__DIR__ . "/substitutions/substitcontenu.php");
	require_once(__DIR__ . "/substitutions/substitparrain.php");
	require_once(__DIR__ . "/substitutions/substitlang.php");
	include_once(__DIR__ . "/substitutions/substitpromo.php");

	function substitutions($texte){
		global $fond, $action;

		$texte = str_replace("#FOND", $fond, $texte);
		$texte = str_replace("#ACTION", $action, $texte);

		$texte = str_replace("#URLPREC", $_SESSION['navig']->urlprec, $texte);
		$texte = str_replace("#URLPAGERET", $_SESSION['navig']->urlpageret, $texte);
		$texte = str_replace("#URLPANIER", urlfond("panier"), $texte);
		$texte = str_replace("#URLCOMMANDER", urlfond("commande"), $texte);
		$texte = str_replace("#URLNOUVEAU", urlfond("nouveau"), $texte);
		/*Ajout*/$texte = str_replace("#DATENOW{Y}", date('Y'), $texte);
		/*Ajout*/$texte = str_replace("#DATENOW{m}", date('m'), $texte);
		/*Ajout*/$texte = str_replace("#DATENOW", date('Y-m-d'), $texte);

		// Supprimer l'éventuel paramètre de déconnexion, en, préservant les éventuels autres.
		$selfurl = supprimer_deconnexion(url_page_courante());

		$parametres = parse_url($selfurl, PHP_URL_QUERY);

		$texte = str_replace("#URLCOURANTEPARAM", $parametres, $texte);
		$texte = str_replace("#URLCOURANTE", escape_ampersand($selfurl), $texte);
        $texte = str_replace("#URLDECONNEXION", escape_ampersand($selfurl . ($parametres != '' ? "&" : "?"). "action=deconnexion"), $texte);
		$texte = str_replace("#URLRECHERCHE", urlfond("recherche"), $texte);

		$texte = str_replace("#URLADRESSE", urlfond("adresse"), $texte);
		$texte = str_replace("#URLPAIEMENT", urlfond("commande"), $texte);
		$texte = str_replace("#URLSOMMAIRE", urlfond(), $texte);
		$texte = str_replace("#URLCOMPTEMODIFIER", urlfond("compte_modifier"), $texte);
		$texte = str_replace("#URLCOMPTE", urlfond("moncompte"), $texte);

		// Substitutions "langue"
		$texte = ActionsLang::instance()->substitutions(ActionsLang::instance()->get_langue_courante(), $texte);

		// Substitutions "devises"
		$texte = ActionsDevises::instance()->substitutions(ActionsDevises::instance()->get_devise_courante(), $texte);

		if(strstr($texte, "#VARIABLE")) $texte = substitvariable($texte);

		if(strstr($texte, "#MESSAGE_")) $texte = substitmessage($texte);
		if(strstr($texte, "#RUBRIQUE_")) $texte = substitrubriques($texte);
		if(strstr($texte, "#PRODUIT_")) $texte = substitproduits($texte);
		if(strstr($texte, "#PANIER_")) $texte = substitpanier($texte);
		if(strstr($texte, "#CLIENT_")) $texte = substitclient($texte);
		if(strstr($texte, "#PAGE_")) $texte = substitpage($texte);
		if(strstr($texte, "#ADRESSE_")) $texte = substitadresse($texte);
		if(strstr($texte, "#COMMANDE_")) $texte = substitcommande($texte);
		if(strstr($texte, "#IMAGE_")) $texte = substitimage($texte);
		if(strstr($texte, "#CARACTERISTIQUE_")) $texte = substitcaracteristique($texte);
		if(strstr($texte, "#DECLINAISON_")) $texte = substitdeclinaison($texte);
		if(strstr($texte, "#DOSSIER_")) $texte = substitdossier($texte);
		if(strstr($texte, "#CONTENU_")) $texte = substitcontenu($texte);
		if(strstr($texte, "#PARRAIN_")) $texte = substitparrain($texte);
		if(strstr($texte, "#PROMO_")) $texte = substitpromo($texte);

		// Traduction du template
		$texte = substitlang($texte);

		if( isset($_GET['errconnex']) && $_GET['errconnex'] == "1") $texte = preg_replace("/\#ERRCONNEX\[([^]]*)\]/", "\\1", $texte);
		else $texte = preg_replace("/\#ERRCONNEX\[([^]]*)\]/", "", $texte);

		// URL d'un fond, forme: #URLFOND(nom-du-fond)
		if (preg_match_all("`\#URLFOND\(([^\),]+)(:?,([^\),]+))*\)`", $texte, $matches, PREG_SET_ORDER)) {

			foreach($matches as $match) {
				$url =  urlfond($match[1], (isset($match[2]) ? $match[3] : false), true);
				$texte = str_replace($match[0], $url, $texte);
			}
		}

		// Ajout d'un paramètre à une URL, forme: #AJOUT_PARAMETRE(url,liste-de-paramètres)
		if (preg_match_all("`\#AJOUTER_PARAMETRE\(([^\),]+)(:?,([^\),]+))*\)`", $texte, $matches, PREG_SET_ORDER)) {

			foreach($matches as $match) {
				$url =  $match[1];

				if (isset($match[2])) {
					if (strstr($url, '?') !== false) $url .= "&amp;".$match[3];
					else $url .= "?".$match[3];
				}

				$texte = str_replace($match[0], $url, $texte);
			}
		}

		$texte = str_replace("index.php", "", $texte);

		return $texte;
	}

?>