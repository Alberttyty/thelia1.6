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
require_once __DIR__ . "/../fonctions/autoload.php";

class Parseur
{
		const PREFIXE   = 'prx';

		const SHOW_TIME = true;
		const ALLOW_DEBUG = true;
		const USE_CACHE = true;

		private static $parse_time;

		private static $vars = [];
		private static $varFond = [];

		protected $cache_dir;

	  static $VARIABLES_CONDITIONNELLES = ['PROMO', 'NOUVEAU'];

		function __construct()
		{
				$this->cache_dir = SITE_DIR . 'client/cache/parseur/';
		}

		function analyse($res)
		{
				$time_start = $this->microtime_float();
				$res = $this->parse_text($res);
				self::$parse_time = $this->microtime_float() - $time_start;

				return $res;
		}

		public static function set_var($variable, $valeur)
		{
				self::$vars['#'.$variable] = $valeur;
		}

		public static function ajouter_variable_conditionnelle($nom)
		{
				self::$VARIABLES_CONDITIONNELLES[] = $nom;
		}

		function parse_text($texte)
		{
				// substitution variables internes
				if (count(self::$vars) > 0) $texte = str_replace(array_keys(self::$vars), array_values(self::$vars), $texte);

				// substition simples
				$texte = substitutions($texte);

				// laisser les infos pour les connectés ou non connectés
				$texte = $this->filtre_connecte($texte);

				// traitement dans le cas d'un formulaire
				$texte = $this->traitement_formulaire($texte);

				$parseur = new Analyse(Variable::lire(self::PREFIXE.'_allow_debug'));

		    $contenu = Variable::lire(self::PREFIXE.'_use_cache') ? $parseur->parse_string_with_cache($texte, $this->cache_dir) : $parseur->parse_string($texte);

		    $texte = $contenu->evaluer();

		    $parseur->terminer();

		    return $texte;
		}

		// Inclusions

		function inclusion(&$res)
		{
				if (preg_match_all('/#INCLURE[\s]*"([^"]*)"/', $res, $matches, PREG_SET_ORDER))	{
			      global $reptpl;

						foreach($matches as $match) {
				        $fichier = $reptpl.str_replace("template/","",$match[1]);
								$contenu = file_get_contents($fichier);

								if ($contenu !== false) {
									$this->inclusion($contenu);
									$res = str_replace($match[0], $contenu, $res);
								}
								else die("Impossible d'ouvrir le fichier inclus $fichier");
						}
				}
		}

		// filtre si connecte
		function filtre_connecte($lect)
		{
				// récupère les infos
				if ($_SESSION['navig']->connecte) {
						$lect = preg_replace("|<THELIA SI CONNECTE>(.*)</THELIA SI CONNECTE>|Us", "\\1", $lect);
						$lect = preg_replace("|<THELIA SI NON CONNECTE>.*</THELIA SI NON CONNECTE>|Us", "", $lect);
				}
				else if (! $_SESSION['navig']->connecte) {
						$lect = preg_replace("|<THELIA SI CONNECTE>.*</THELIA SI CONNECTE>|Us", "", $lect);
						$lect = preg_replace("|<THELIA SI NON CONNECTE>(.*)</THELIA SI NON CONNECTE>|Us", "\\1", $lect);
				}

				return $lect;
		}

    /**
     *
     * @param string &$res
     */
    public static function setVarFond(&$res)
    {
        preg_match_all("/#PARAM_FOND_([a-zA-Z0-9_]+)[\s]*=[\s]*([^\s]*)/", $res, $matches, PREG_SET_ORDER);

        foreach($matches as $match) {
            self::$varFond[strtolower($match[1])] = $match[2];
            $res = str_replace($match[0], '', $res);
        }
    }

    /**
     *
     * @param string $nom
     * @param string $filtre
     * @param bool $defaut
     * @param bool $purifier
     */
    public static function lireVarFond($nom, $filtre='int', $defaut = false, $purifier = 1)
    {
        $nom = strtolower($nom);

        if (isset(self::$varFond[$nom])) {
            if (preg_match("/^([^\+]*)\+(.*)$/", $filtre, $resfiltre)) {
                $filtre = $resfiltre[1];
                $complement = $resfiltre[2];
            }
            else $complement = "";

            $val = filtrevar(self::$varFond[$nom], $filtre, $complement, $purifier);

            if ($val!=='') return $val;
        }

        return $defaut;
    }

		function traitement_formulaire($res)
		{
				if (isset($_REQUEST['errform']) && intval($_REQUEST['errform']) == 1)
						$res = $this->traitement_formulaire_client($res);

				if (isset($_REQUEST['erradr']) && intval($_REQUEST['erradr']) == 1)
						$res = $this->traitement_formulaire_adresse($res);

				return $res;
		}

		function controle_formulaire($objet, $mapping, $res)
		{
				foreach($mapping as $var => $attribut) {
						// #VAR[xxxxx]
						$res = preg_replace(
									"/\#$var\[([^]]*)\]/",
									$objet->$attribut == "" ? "\\1" : '',
									$res
						);

						// #VAR
						$res = str_replace("#$var", $objet->$attribut, $res);
				}

				return $res;
		}

		function traitement_formulaire_adresse($res)
		{
	  		// Les infos stockées en session
	   		$mapping = array(
	   					'LIBELLE' => 'libelle',
	   					'RAISONID' => 'raison', 'PRENOM' => 'prenom', 'NOM' => 'nom',
	   					'ADRESSE1' => 'adresse1', 'ADRESSE2' => 'adresse2', 'CPOSTAL' => 'cpostal', 'VILLE' => 'ville', 'PAYS' => 'pays',
	   					'EMAIL' => 'email',
	   					'TEL' => 'tel', 'ENTREPRISE' => "entreprise"
	   		);

	   		$res = $this->controle_formulaire($_SESSION['navig']->formadr, $mapping, $res);

	   		return $res;
		}

		function traitement_formulaire_client(&$res)
		{
				require_once(__DIR__ . "/Raison.class.php");

				// L'existence de l'email
	      if ($_SESSION['navig']->formcli->email != "") {
	        	$client = new Client();
	          if( $client->existe($_SESSION['navig']->formcli->email)) $res = preg_replace("/\#EXISTE\[([^]]*)\]/", "\\1", $res);
	   		}

	   		$res = preg_replace("/\#EXISTE\[[^]]*\]/", "", $res);

	   		// Les infos stockées en session
	   		$mapping = array(
	   					'RAISONID' => 'raison', 'PRENOM' => 'prenom', 'NOM' => 'nom',
	   					'ENTREPRISE' => 'entreprise', 'SIRET' => 'siret', 'INTRACOM' => 'intracom',
	   					'ADRESSE1' => 'adresse1', 'ADRESSE2' => 'adresse2', 'ADRESSE3' => 'adresse3' /* WTF ?*/, 'CPOSTAL' => 'cpostal', 'VILLE' => 'ville', 'PAYS' => 'pays',
	   					'EMAIL' => 'email',
	   					'MOTDEPASSE' => 'motdepasse',
	   					'TELFIXE' => 'telfixe', 'TELPORT' => 'telport'
	   		);

	   		$res = $this->controle_formulaire($_SESSION['navig']->formcli, $mapping, $res);

	   		// Le parrain
	   		$tmpparrain = new Client();
	   		$tmpparrain->charger_id($_SESSION['navig']->formcli->parrain);

	   		$res = str_replace("#PARRAIN", $tmpparrain->email, $res);

	   		// Les RAISONn et CHECKn
	    	$raisons = CacheBase::getCache()->query("select id from ".Raison::TABLE);

	   		if ($raisons) foreach($raisons as $raison) {
		   			$sel = $_SESSION['navig']->formcli->raison == $raison->id;

		   			$res = str_replace("#RAISON$raison->id", $sel ? 'selected="selected"' : '', $res);
		   			$res = str_replace("#CHECK$raison->id", $sel ? 'checked="checked"' : '', $res);
	   		}

	   		// Compatibilité < 1.5.2
				if ($_SESSION['navig']->formcli->raison == "") {
				     $res = str_replace(
				     			"#RAISON0",
				     			$_SESSION['navig']->formcli->raison == "" ? 'selected="selected"' : '',
				     			$res
				     );
				}

				return $res;
		}

		function post($res)
		{
				// Traitement de #HEADER{}
				if (preg_match_all('/#HEADER{([^}]+)}/', $res, $matches, PREG_SET_ORDER)) {
						foreach($matches as $match) {
								$res = str_replace($match[0], '', $res);
				        if(strpos($match[1],'301') !== false) header($match[1],false,301);
				        elseif(strpos($match[1],'302') !== false) header($match[1],false,302);
								else header($match[1]);
						}
				}


				if (Variable::lire(self::PREFIXE.'_show_time')) {
						$res = str_ireplace('</html>', '<!-- Page parsée et évaluée en '.round(self::$parse_time, 4)." secondes -->\n</html>", $res);
				}

				if (Analyse::$debug_text) {
						if (strstr($res, '<body>')) $res = str_ireplace('<body>', '<body>\n' . Analyse::$debug_text, $res);
						else $res = Analyse::$debug_text . $res;
				}

				return $res;
		}

		function microtime_float()
		{
		   list($usec, $sec) = explode(" ", microtime());
		   return ((float)$usec + (float)$sec);
		}
}

// Activation des traces
define ('DEBUG_PARSER', isset($_REQUEST['debug_parser']));
define ('DEBUG_EVAL'  , isset($_REQUEST['debug_eval']));

?>
