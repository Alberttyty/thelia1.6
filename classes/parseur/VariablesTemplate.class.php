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
// ------------------------------------------------------------------
// Traitement de variables de template, avec la syntaxe:
//   #SET{var, valeur}, #GET{var [, defaut]} et #ENV{var [, defaut]}
// ------------------------------------------------------------------

require_once __DIR__ . "/../../fonctions/autoload.php";

class VariablesTemplate {
	private static $instance = null;
	private static $symboles = array();
	private static $modules = false;

	// Trouver tous les plugins avec une fonction post() et #FILTRE dans leur code
	private function trouver_modules_filtre() {
		if (self::$modules !== false) return;

		self::$modules = array();

		// Trouver tous les modules ayant '#FILTRE' dans leur code (hack pour la perf)
		$liste = ActionsModules::instance()->lister(false, true);

		foreach($liste as $module) {

			try {
				$class_name = ucfirst($module->nom);
				$class_file = ActionsModules::instance()->lire_chemin_module . "/" . $class_name. ".class.php";

				if (file_exists($class_file)) {
					$code = file_get_contents($class_file);

					if (strpos($code, '#FILTRE') !== false)	{
						$instance = ActionsModules::instance()->instancier($module->nom);
						if (method_exists($instance, 'post')) self::$modules[] = $clazz;
					}
				}
			} catch (Exception $e) {}
		}
	}

	public static function analyser($texte) {
		if (self::$instance == null) self::$instance = new VariablesTemplate();
		return self::$instance->demarrer($texte);
	}

	private function syntax_error($text) {
    	die("Erreur de syntaxe: $text");
    }

    // Appliquer les filtres de base et les filtres utilisateur
    private function appliquer_filtres($texte) {
    	global $res;

    	if (strpos($texte, '#FILTRE') !== false) {
    		$this->trouver_modules_filtre();
    		$tmp_res = $res;

    		// Filtres standard
    		// $res = filtres($texte);
    		Filtres::exec($texte);
			$res = $texte;

    		// Filtres utilisateur
    		foreach(self::$modules as $module) {
    			$module->post();
    		}

    		$res = $tmp_res;
     	}

   		return $texte;
    }


    // Récupérer une valeur de variable, avec filtrage éventuel, et valeur par defaut
    // si la variable ne peut être valuée.
    private function filtrer_var($type, $var, $defaut = '') {
    	if ($type == 'ENV' && isset($_REQUEST[$var])) {
   			$var = is_array($_REQUEST[$var]) ? implode(',', $_REQUEST[$var]) : $_REQUEST[$var];
    		return strip_tags($var);
     	} else if ($type == 'GET' && isset(self::$symboles[$var]) && self::$symboles[$var] != '') {
    		return  self::$symboles[$var];
    	} else if ($type == 'SESSION' && isset($_SESSION["thelia_$var"]) && $_SESSION["thelia_$var"] != '') {
    		return  $_SESSION["thelia_$var"];
    	}

    	return $defaut;
    }

    private function analyser_nom_var() {
    	$var = trim(next($this->tokens));
    	if (! preg_match('/^[\w\:]+$/', $var)) $this->syntax_error("Nom de variable invalide: '$var'");
    	return $var;
    }

    private function analyser_set($directive) {
    	//Parser::echo_debug("get_set()");

	    $tok = next($this->tokens);

	    if ($tok == '{') {
	    	$var = $this->analyser_nom_var();

	    	if (next($this->tokens) == ',') {
	    		// Lire jusqu'� la fermeture
	    		$val = $this->analyser_contenu('}');

	    		if (current($this->tokens) == '}') {
	    			$val = $this->appliquer_filtres(trim($val));

	    			if ($directive == 'SET') {
	    				// Evaluer les filtres THELIA sur la valeur
	    				self::$symboles[$var] = $val;
	    			} else if ($directive == 'SESSION_SET') {
	    				$_SESSION["thelia_$var"] = $val;
	    			}

	    			// Parser::echo_debug("FIN $directive $var = $val");
	    			return '';
					
	    		} else $this->syntax_error("$directive $var: } attendu, $tok trouv�.");
				
	    	} else if (current($this->tokens) == '}') {
    			if ($directive == 'SET') unset(self::$symboles[$var]);
    			else if ($directive == 'SESSION_SET') unset($_SESSION["thelia_$var"]);
    			return '';
				
    		} else $this->syntax_error("$directive $var: ',' attendu $tok trouv�.");
			
	    } else return $tok;
    }

    private function analyser_get($type) {
    	//Parser::echo_debug( "analyser_get()");

    	// Lire jusqu'� la fermeture
	    if (($tok = next($this->tokens)) == '{') {
	    	$var = $this->analyser_nom_var();

	    	if (($tok = next($this->tokens)) == ',') {
	    		// Une valeur par defaut - appliquer les filtres Thelia à cette valeur
	    		$defaut = $this->appliquer_filtres(trim($this->analyser_contenu('}')));
	    		return $this->filtrer_var($type, $var, $defaut);
				
	    	} else if ($tok == '}') {
	    		// Pas de valeur par defaut
	    		return $this->filtrer_var($type, $var);
				
	    	} else $this->syntax_error("$type $var: ',' ou '}' attendu, $tok trouv�.");
	    }
	    else return $tok;
    }

    private function analyser_contenu($stopchar = '') {
    	// Parser::echo_debug( "analyser_contenu($stopchar)");

    	$content = '';

    	while (($tok = next($this->tokens)) !== false) {
    		if ($tok == '#') {
    			$tok = next($this->tokens);

    			if ($tok == 'SET' || $tok == 'SESSION_SET') $this->analyser_set($tok);
    			else if ($tok == 'GET' || $tok == 'ENV' || $tok == 'SESSION') $content .= $this->analyser_get($tok);
     			else $content .= '#'.$tok;
				
    		} else if ($stopchar != '' && $tok == $stopchar) break;
    		else $content .= $tok;
    	}

		// Parser::echo_debug( "parsing done.");
    	return $content;
    }

    private function demarrer($chaine) {
    	$this->tokens = preg_split('/(\#|\{|\}|,)/', $chaine, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        // Comme on fait un next() dans parse content, insérer une valeur non significative en début de tableau
        array_unshift($this->tokens, '');

    	//Parser::echo_debug("Tokens:", $this->tokens);
    	$content = $this->analyser_contenu();

    	//Parser::echo_debug("Content:", $content);
    	return $content;
    }

}
?>