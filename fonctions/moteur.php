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
/* Moteur */
require_once(__DIR__ . "/../fonctions/error_reporting.php");
require_once(__DIR__ . "/mutualisation.php");
require_once(__DIR__ . "/autoload.php");

/* Inclusions nécessaires avant ouverture de la session */
$modules = ActionsModules::instance()->lister(false, true);

foreach($modules as $module) {
	try {
		$path = ActionsModules::instance()->lire_chemin_module($module->nom) . "/inclure_session.php";
		if (file_exists($path))	include_once($path);
	} catch (Exception $e) {}
}

session_start();
header("Content-type: text/html; charset=utf-8");

require_once(__DIR__ . "/../fonctions/boucles.php");
require_once(__DIR__ . "/../fonctions/substitutions.php");
require_once(__DIR__ . "/../fonctions/action.php");
require_once(__DIR__ . "/../fonctions/divers.php");

ActionsModules::instance()->appel_module("predemarrage"); // ajout 1.4.3

require_once(__DIR__ . "/../fonctions/lire.php");
require_once(__DIR__ . "/../fonctions/url.php");

// initialisation des variables du couple php/html
if(!isset($res)) $res="";
if(!isset($lang)) $lang="";
if(!isset($devise)) $devise="";
if(!isset($securise)) $securise=0;
if(!isset($panier)) $panier=0;
if(!isset($nopageret)) $nopageret=0;
if(!isset($reset)) $reset=0;
if(!isset($transport)) $transport=0;
if(!isset($reptpl) && !isset($fond)) $reptpl="template/";
if(!isset($sortie)) $sortie=true;

// création de la session si non existante
if(! isset($_SESSION["navig"])) $_SESSION["navig"] = new Navigation();

// Déterminer le fond de page
if (! isset($fond))
	$fond = lireParam('fond', 'string'); // 'string_iso_strict');
else {
	// compatibilité ancien THELIA
	$fond = str_ireplace('.html', '', $fond);
}

// Par defaut, utiliser le template 'index'
if ($fond == '') $fond = 'index';


/* Définition de la langue courante, dans l'ordre suivant :
 * $lang déjà défini par un plugin
 * 2) paramètre 'lang' dans l'URL courante
 * 3) Correspondance URL courante <-> langue
 * 4) Langue précédemment stockée en session
 * 5) Defaut
 */
if (empty($lang)) {
	$l = new Lang();

	if (isset($_REQUEST['lang'])) {
		// Parametre dans l'URL
		$lang = lireParam('lang', 'int');
	} else if (Variable::lire('un_domaine_par_langue') != 0 && $l->charger_url($_SERVER['SERVER_NAME'])) {
		// Langue du domaine
		$lang = $l->id;
	} else if (ActionsLang::instance()->id_langue_courante_defini()) {
		$lang = ActionsLang::instance()->get_id_langue_courante();
	} else {
		// Langue par defaut
		$lang = ActionsLang::instance()->get_id_langue_defaut();
	}
}



// Définition de la devise courante
if (empty($devise)) {
	if (isset($_REQUEST['devise']))
		$devise = lireParam('devise', 'int');
	else if (ActionsDevises::instance()->id_devise_courante_definie()) {
		$devise = ActionsDevises::instance()->get_id_devise_courante();
	} else {
		// Devise par defaut
		$devise = ActionsDevises::instance()->get_id_devise_defaut();
	}
}

// Les autres paramètres présents dans les URL
$vars = array(
		'action'			=> "string",
		'append'			=> "int",
		'id'				=> "int",
		'id_parrain'		=> "int",
		'nouveau'			=> "int",
		'parent'			=> "int",
		'ref'				=> "string",
		'quantite'			=> "float",
		'article'			=> "int",
		'type_paiement'		=> "int",
		'code'				=> "string",
		'entreprise'		=> "string",
		'siret'				=> "string",
		'intracom'			=> "string",
		'parrain'			=> "string",
		'motdepasse1'		=> "string",
		'motdepasse2'		=> "string",
		'raison'			=> "int",
		'prenom'			=> "string",
		'libelle'			=> "string",
		'nom'				=> "string",
		'adresse1'			=> "string",
		'adresse2'			=> "string",
		'adresse3'			=> "string",
		'cpostal'			=> "string",
		'ville'				=> "string",
		'pays'				=> "int",
		'telfixe'			=> "string",
		'telport'			=> "string",
		'tel'				=> "string",
		'email1'			=> "string",
		'email2'			=> "string",
		'email'				=> "string",
		'motdepasse'		=> "string",
		'adresse'			=> "int",
		'id_rubrique'		=> "int",
		'id_dossier'		=> "int",
		'nouveaute'			=> "int",
		'promo'				=> "int",
		'stockmini'			=> "float",
		'page'				=> "int",
		'totbloc'			=> "int",
		'id_contenu'		=> "int",
		'caracdisp'			=> "int+\-",
		'reforig'			=> "string",
		'motcle'			=> "string",
		'id_produit'		=> "int",
		'classement'		=> "string",
		'prixmin'			=> "float",
		'prixmax'			=> "float",
		'id_image'			=> "int",
		'declinaison'		=> "string",
		'declidisp'			=> "int+\-",
		'declival'			=> "string",
		'declistock'		=> "float",
		'commande'			=> "string",
		'caracteristique'	=> "int+\-",
		'caracval'			=> "string",
		'url'				=> "string",
		'nopageret'			=> "int",
		'obligetel'			=> "int",
		'urlok'				=> "string",
		'urlerr'			=> "string",
        'id_commande'       => "int",
        'id_paiement'       => "int"
	);

	foreach ($vars as $nomvar => $typevar) {
		$$nomvar = lireParam($nomvar, $typevar);
	}

	// Compatibilité 1.4 qui intialise $append à 0 et non pas à ''
	$append = intval($append);

	$rewrite_active = Variable::lire("rewrite", 0);
	// Si le rewrite est activé, on doit rediriger les pages non réécrites vers les pages réécrites (duplicate content).
    $tab_fond_rewrite = array("rubrique", "produit", "dossier", "contenu");
    if(isset($_GET['fond']) && in_array($_GET['fond'], $tab_fond_rewrite)){
        if($rewrite_active == 1){
            $redir = new Reecriture();
            if($redir->charger_url_classique($_SERVER['QUERY_STRING'], ActionsLang::instance()->get_id_langue_courante(), 1)){
                header("HTTP/1.1 301 Moved Permanently");
                redirige(urlfond() . "/" . $redir->url);
            }
        }
    }

	// Chargement du contexte dans le cas d'une réécriture
	if($url != "") {

		$reecriture = new Reecriture();

		if ($reecriture->charger($url)) {

			if (!$reecriture->actif) {
				$redir = new Reecriture();
				if ($redir->charger_param($reecriture->fond, $reecriture->param, $reecriture->lang, 1)) {
					header("HTTP/1.1 301 Moved Permanently");
					redirige(urlsite($reecriture->lang) . "/" . $redir->url);
				}
			}

			// Si un changement de langue est demandé
			if (isset($_GET['lang'])) {

				// Rediriger vers l'URL dans cette langue, si elle existe
				$redir = new Reecriture();

				if ($redir->charger_param($reecriture->fond, $reecriture->param, $lang, 1)) {
					header("HTTP/1.1 301 Moved Permanently");
					redirige(urlsite($lang) . "/" . $redir->url);
				}
			} else {

				$urlcurr = urlsite($lang);
				$urlnext = urlsite($reecriture->lang);

				if ($urlcurr != $urlnext) {
					// URLs différentes: rediriger vers le domaine spécifique de la langue.
					redirige($urlnext . '/' . $reecriture->url);
				} else {
					// Mêmes URLs => fixer la langue courante est celle définie par l'URL ré-écrite, inutile de rediriger.
					$lang = $reecriture->lang;
				}
			}

			$fond = $reecriture->fond;

			switch($fond) {
				case 'rubrique' :
					$_REQUEST['fond'] = 'rubrique';
					$_REQUEST['id_rubrique'] = $id_rubrique = lireVarUrl("id_rubrique", $reecriture->param);
					break;

				case 'produit' :
					$_REQUEST['fond'] = 'produit';
					$_REQUEST['id_produit'] = $id_produit = lireVarUrl("id_produit", $reecriture->param);
					$_REQUEST['id_rubrique'] = $id_rubrique = lireVarUrl("id_rubrique", $reecriture->param);
					break;

				case 'dossier' :
					$_REQUEST['fond'] = 'dossier';
					$_REQUEST['id_dossier'] = $id_dossier = lireVarUrl("id_dossier", $reecriture->param);
					break;

				case 'contenu' :
					$_REQUEST['fond'] = 'contenu';
					$_REQUEST['id_contenu'] = $id_contenu = lireVarUrl("id_contenu", $reecriture->param);
					$_REQUEST['id_dossier'] = $id_dossier = lireVarUrl("id_dossier", $reecriture->param);
					break;
			}

			ActionsModules::instance()->appel_module("lireVarUrl", $reecriture);

			// la réécriture n'est pas activé mais on demande une url réécrite.
			// on doit envoyer sur l'url non réécrite (duplicate content)
			if($rewrite_active == 0) {
				header("HTTP/1.1 301 Moved Permanently");
				redirige(urlfond() . "/?fond=" . $reecriture->fond . "&lang=" . $reecriture->lang . $reecriture->param);
			}
		} else {
			header("HTTP/1.1 404 Not Found");
			$fond="404";
		}
	}

    // on supprime index.php s'il est présent (duplicate content)
    if(strstr($_SERVER['REQUEST_URI'], "index.php") && $_SERVER['REQUEST_METHOD'] == "GET"){
        $redir_url = urlfond();

        if(! empty($_SERVER['QUERY_STRING']) ) $redir_url .= "?" . $_SERVER['QUERY_STRING'];

        header("HTTP/1.1 301 Moved Permanently");
        redirige($redir_url);
    }

	// URL précédente
	if(isset($_SERVER['HTTP_REFERER'])) $_SESSION["navig"]->urlprec = $_SERVER['HTTP_REFERER'];

	// Langue
	ActionsLang::instance()->set_id_langue_courante($lang);

	// Devise
	ActionsDevises::instance()->set_id_devise_courante($devise);

	// fonctions à éxecuter avant le moteur
	ActionsModules::instance()->appel_module("demarrage");

	// Actions
	switch($action){
		case 'ajouter' :
			ajouter($ref, $quantite, $append, $nouveau, $parent);
			break;

		case 'supprimer' :
			supprimer($article);
			break;

		case 'modifier' :
			modifier($article, $quantite);
			break;

		case 'connexion' :
			connexion($email,$motdepasse, $urlok, $urlerr);
			break;

		case 'deconnexion' :
			deconnexion();
			break;

		case 'paiement' :
			paiement($type_paiement);
			break;

		case 'transport' :
			transport($id);
			break;

		case 'creercompte' :
			creercompte($raison, $entreprise, $siret, $intracom, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $pays, $telfixe, $telport, $email1, $email2, $motdepasse1, $motdepasse2, $parrain, $obligetel, $urlok, $urlerr);
			break;

		case 'modifiercompte' :
			modifiercompte($raison, $entreprise, $siret, $intracom, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $pays, $telfixe, $telport, $email1, $email2, $motdepasse1, $motdepasse2, $obligetel, $urlok, $urlerr);
			break;

		case 'modifiermotdepasse' :
			modifiermotdepasse($motdepasse1, $motdepasse2, $urlok, $urlerr);
			break;

		case 'creerlivraison' :
			creerlivraison($id, $libelle, $raison, $entreprise, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $tel, $pays, $urlok, $urlerr);
			break;

    	case 'supprimerlivraison' :
			supprimerlivraison($id);
			break;

		case 'modifierlivraison' :
			modifierlivraison($id, $libelle, $raison, $entreprise, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $tel, $pays, $urlok, $urlerr);
			break;

		case 'modadresse' :
			modadresse($adresse);
			break;

		case 'codepromo' :
			codepromo($code);
			break;

		case 'chmdp' :
			chmdp($email, $urlok, $urlerr);
			break;

    	case 'reprise_paiement':
			reprise_paiement($id_paiement, $id_commande);
			break;
	}

	// fonctions à éxecuter avant ouverture du template
	ActionsModules::instance()->appel_module("pre");

	// chargement du squelette
	if($res == ""){

		$tpl = $reptpl . $fond;

		// $tpl doit impérativement être dans le répertoire $reptpl, ou un de ses sous répertoires.
        $path_tpl = rtrim(realpath(dirname($tpl)), '/');
        $path_reptpl = rtrim(realpath($reptpl), '/');

		if (strpos($path_tpl, $path_reptpl) !== 0) {
			die("FOND Invalide: $tpl");
		}

		if (!file_exists($tpl)) {
			$tpl .= ".html";
			if(!file_exists($tpl)) die("Impossible d'ouvrir $fond ($tpl)");
		}

		$res = file_get_contents($tpl);
	}

	// Chargement du fichier de langue (si existant) dans le répertoire où se trouve le fond.
	// On essaie de charger <code_langue>.php, et s'il n'existe pas, <id_langue>.php
	if (! empty($path_reptpl)) {

		$langobj = new Lang($lang);

		$lang_file = $path_reptpl . "/lang/" .  strtolower($langobj->code) . ".php";

		if (! file_exists($lang_file))
			$lang_file = $path_reptpl . "/lang/" .  $langobj->id . ".php";

		@include_once($lang_file);

	}

    Parseur::setVarFond($res);

    // Lire les options définies dans le template
    $securise = Parseur::lireVarFond('securise', 'int', 0);
    $nopageret = Parseur::lireVarFond('nopageret', 'int', 0);
    $reset = Parseur::lireVarFond('reset', 'int', 0);
    $panier = Parseur::lireVarFond('panier', 'int', 0);
    $transport = Parseur::lireVarFond('transport', 'int', 0);

	// fonctions à éxecuter après la lecture des variables de fond
	ActionsModules::instance()->appel_module("varfond");

	// Page retour
	// Supprimer le paramètre "déconnexion" de l'url page retour
	if(! $nopageret) $_SESSION["navig"]->urlpageret = supprimer_deconnexion(url_page_courante());
	else if($_SESSION["navig"]->urlpageret=="") $_SESSION["navig"]->urlpageret = urlfond();

	// Sécurisation
	if($securise && ! $_SESSION["navig"]->connecte) {
		redirige(urlfond("connexion"));
	}

	// Vérif transport
	if($transport && ! $_SESSION["navig"]->commande->transport) {
		redirige(urlfond("adresse"));
	}

	// Vérif panier
	if($panier && ! $_SESSION["navig"]->panier->nbart) { redirige(urlfond()); }

  $parseur = new Parseur();

	// fonctions à éxecuter avant les inclusions
	ActionsModules::instance()->appel_module("inclusion");

	// inclusion
	$parseur->inclusion($res);

	// inclusions des plugins
	ActionsModules::instance()->appel_module("action");

	$res = $parseur->analyse($res);

	ActionsModules::instance()->appel_module("analyse");

  Filtres::exec($res);

	$res = $parseur->post($res);

	// inclusions des plugins filtres
	ActionsModules::instance()->appel_module("post");

	Tlog::ecrire($res);

	// Résultat envoyé au navigateur
	$res = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $res);

	if ($sortie) echo $res;

	// fonctions à éxecuter apres l'affichage du template
	ActionsModules::instance()->appel_module("apres");

	// Reset de la commande
	if ($reset) {
      $_SESSION["navig"]->commande = new Commande();
      $_SESSION["navig"]->panier = new Panier();
			$_SESSION['navig']->promo = new Promo();
	}

?>
