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
require_once __DIR__ . "/../../fonctions/autoload.php";

class Analyse
{
		public $contenu;
		public $tokens;

		public $pile_nom_boucle;
		public $pile_boucle_courante;

		private $in_comment = false;

		private static $no_debug;

		public static $debug_text;

    function __construct($allow_debug)
    {
    		self::$no_debug = ! ($allow_debug && (DEBUG_PARSER || DEBUG_EVAL));

        $this->tokens = array();
        $this->pile_nom_boucles = array();
        $this->pile_boucle_courante = array();

        self::$debug_text = false;
    }

    function terminer()
    {
        if (! self::$no_debug) {
		        self::$debug_text = '
			        	<div style="border: 1px solid black; margin: 5px; background-color: white; color: black; text-align: left; font-size: 11px;">
			        	<div style="border-bottom: 1px solid black; margin: 0; padding: 5px; background-color: #f0f0f0; font-weight: bold;">Information de debug du parser</div>
			        	<pre style="margin: 0; padding: 5px; height: 200px; overflow: scroll;">'
			    		. self::$debug_text
			    		.'</pre></div>'
			    	;
        }
    }

		static function strlen_cmp($a, $b)
		{
		    $la = strlen($a);
		    $lb = strlen($b);

		    if ($la == $lb) return 0;

		    return ($la > $lb) ? -1 : 1;
		}

		public static function echo_debug()
		{
				if (self::$no_debug) return;

				$text = '';
				$numargs = func_num_args();

		    for($idx = 0; $idx < $numargs; $idx++) {
		    		$arg = func_get_arg($idx);
		        $text .= is_scalar($arg) ? $arg : print_r($arg, true);
		    }

		    self::$debug_text .= '<pre>[DEBUG] ' . htmlspecialchars($text)."</pre>\n";
		}

		function parse_string(&$filecontents)
		{
	        $this->tokens = preg_split("/( |\t|\n|\r|<|>|\\#|\")/", $filecontents, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

	        // Comme on fait un next() dans parse content, insérer une valeur non significative en début de tableau
	        array_unshift($this->tokens, '');

	        // if (DEBUG_PARSER) {Analyse::echo_debug("Tokens: ", $this->tokens); }

	        return $this->parse_content();
		}

    function parse_string_with_cache(&$filecontents, $cache_dir)
    {
        if (! is_dir($cache_dir)) {
	        	if (mkdir($cache_dir, 0777, true) === false) {
	        			die('Impossible de créer le répertoire '.$cache_dir.'. Vérifiez les droits d\'accès');
	        	}
        }

        $this->cleanup_cache($cache_dir);

	    	$cache_file = $cache_dir . hash('md5', $filecontents) . '.cache';

	    	if (file_exists($cache_file)) {
		    		// Mettre à jour la date du fichier: les fichiers les plus souvent accédés restent plus longtemps dans le cache.
		    		@touch($cache_file);
		    		return unserialize(file_get_contents($cache_file));
	    	}
	    	else {
	        	$data = $this->parse_string($filecontents);

	        	file_put_contents($cache_file, serialize($data));

	        	return $data;
	    	}
    }

    public static function cleanup_cache($cache_dir, $force = 0)
    {
	    	// Doit-on purger le cache ?
	    	$last_check   = intval(Variable::lire(Parseur::PREFIXE.'_cache_check_time'));
				$check_period = intval(3600 * Variable::lire(Parseur::PREFIXE.'_cache_check_period'));

	    	if ($force == 0 && time() - $last_check < $check_period) return;

	    	Variable::ecrire(Parseur::PREFIXE.'_cache_check_time', time());

	    	$cache_file_lifetime = 3600 * Variable::lire(Parseur::PREFIXE.'_cache_file_lifetime');

	    	if ($dh = @opendir($cache_dir)) {
		    		while ($file = readdir($dh)) {
			    			if (strstr($file, '.cache') !== false) {
					    			$path = $cache_dir . $file;
				    				$filemtime = @filemtime($path);

					    			if (! $filemtime || (time() - $filemtime) >= $cache_file_lifetime) {
					    					@unlink($path);
					    			}
			    			}
		    		}

		    		@closedir($dh);
	    	}
    }

    function parse_args()
		{
        $args = '';

        $in_quote = false;

        while (1) {
            $tok = next($this->tokens);

            //if (DEBUG_PARSER) { Analyse::echo_debug("Parse args: tok='$tok', in_quote=$in_quote"); }

            if ($tok == '#') {
                $token = next($this->tokens);

                if (preg_match('/([A-Z0-9_]+)/', $token, $varname) > 0) {
                    $tok = '#'.$varname[1];
                    //if (DEBUG_PARSER) { Analyse::echo_debug("new arg var: '$tok'"); }
                }

                $this->add_var($tok);

                // Il faut placer dans les args la valeur originale (sinon bug. Ex: #PROMO[X][Y])
                $tok = '#'.$token;
            }
            else if ($tok == '"') $in_quote = ! $in_quote;
            else if ($tok == '>') if (! $in_quote) break;
            else if ($tok === FALSE) break;

            $args .= $tok;
        }

        return $args;
    }

    function add_var($token)
    {
        // Ne pas prendre en compte les filtres et les get/set
				if (strstr($token, 'FILTRE_') !== FALSE
           ||
       	   strstr($token, 'GET{') !== FALSE
       	   ||
       	   strstr($token, 'SET{') !== FALSE) return;


        //if (DEBUG_PARSER) { Analyse::echo_debug("Variable: $token");}
        $count = count($this->pile_boucle_courante);
        // Pas de boucle ouverte
        if ($count == 0) return;

        //$boucle_courante = end($this->pile_boucle_courante);
        $boucle_courante = $this->pile_boucle_courante[$count-1];

        //if (DEBUG_PARSER) { Analyse::echo_debug("boucle courante: ", $boucle_courante); }

        // La boucle n'est pas une boucle simple -> on ne stocke pas les variables
        if ($boucle_courante->type() != PexToken::TYPE_BOUCLE_SIMPLE) return;

        if (preg_match('/([A-Z0-9_]+)/', $token, $varname) > 0) {
            if (! in_array($varname[1], $boucle_courante->variables)) {
                $boucle_courante->variables[] = $varname[1];
                //if (DEBUG_PARSER) { Analyse::echo_debug("Variables de $boucle_courante->nom", $boucle_courante->variables);}
            }
        }
    }

    function controle_fermeture_boucle($nom_boucle)
    {
        $nom_pile = array_pop($this->pile_nom_boucles);

        //if (DEBUG_PARSER) Analyse::echo_debug("Pile boucles: required: '$nom_boucle', popped: '/$nom_pile', ", $this->pile_nom_boucles);

        if ($nom_boucle != '/' . $nom_pile) {
	        	if ($nom_pile == '') die("Erreur de syntaxe: $nom_boucle: balise de fin sans balise de début.");
	        	else die("Erreur de syntaxe: $nom_boucle trouvé, /$nom_pile attendu.");
        }
    }

    function process_token(&$atoken)
    {
        //if (DEBUG_PARSER) { Analyse::echo_debug("enter process_token ", $atoken);}

        $token_type = PexToken::TXT;

        if ($atoken == '<') {
        		$no_match = false;

            $token = next($this->tokens);

            if (DEBUG_PARSER) Analyse::echo_debug("Next PexToken:[$token]");

            // Optimisation (gain: ~= 0,1 sec. sur index standard)
            if ($token[0] != 'T' && strpos($token, '/T') !== 0 && strpos($token, '//T') !== 0 && $token[0] != 'R' && strpos($token, '/R') !== 0) {
	            	//if (DEBUG_PARSER) { Analyse::echo_debug("N'est pas une boucle thelia");}
	            	$no_match = true;
            }
            else {
		            // Get token type
		            if (strpos($token, 'THELIA_') === 0) $token_type = PexToken::OBS;
		            else if (strpos($token, '/THELIA_') === 0) $token_type = PexToken::FBS;
		            else if (strpos($token, 'TEST_') === 0) $token_type = PexToken::OBT;
		            else if (strpos($token, '/TEST_') === 0) $token_type = PexToken::EBT;
		            else if (strpos($token, '//TEST_') === 0) $token_type = PexToken::FBT;
		            else if (strpos($token, 'T_') === 0) $token_type = PexToken::OBC;
		            else if (strpos($token, '/T_') === 0) $token_type = PexToken::EBC;
		            else if (strpos($token, '//T_') === 0) $token_type = PexToken::FBC;
		            else if (strpos($token, 'REM') === 0) $token_type = PexToken::OCM;
		            else if (strpos($token, '/REM') === 0) $token_type = PexToken::FCM;
		            else if (strpos($token, 'REPETER') === 0) $token_type = PexToken::OBR;
		            else if (strpos($token, '/REPETER') === 0) $token_type = PexToken::FBR;
		            else if (strpos($token, 'T:') === 0) $token_type = PexToken::OBCV;
		            else if (strpos($token, '/T:') === 0) $token_type = PexToken::EBCV;
		            else if (strpos($token, '//T:') === 0) $token_type = PexToken::FBCV;
		            else {
		            		//if (DEBUG_PARSER) { Analyse::echo_debug("Token type texte");}
			            	$no_match = true;
		            }
            }

            if ($no_match) {
                prev($this->tokens);
                $token = $atoken;
            }
        }
        // Variables
        else if ($atoken == '#') {
	        	// Traiter les cas similaires à ##REF
	        	$tmp = next($this->tokens);

	        	if ($tmp == '#') {
		        		$token = '#';
		        		prev($this->tokens);
	        	}
	        	else {
	            	$token = '#' . $tmp;
	            	$this->add_var($token);
	        	}
        }
        else $token = $atoken;

        //if (DEBUG_PARSER) { Analyse::echo_debug( "Token:[$token], type $token_type"); }

        // Dans un commentaire, on attend la fin sans rien analyser
        if ($this->in_comment && $token_type !== PexToken::FCM) {
	        	//if (DEBUG_PARSER) { Analyse::echo_debug("ignore: $token_type:", $token); }
	        	return 'vide';
        }

        // BOUCLE SIMPLE et boucle REPETER
        if ($token_type === PexToken::OBS || $token_type === PexToken::OBR) {
	        	if ($token_type === PexToken::OBS) $boucle = new BoucleSimple(substr($token, 7));
	          else $boucle = new BoucleRepeter(substr($token, 8));

            array_push($this->pile_nom_boucles, $token);

            // Parse args se fait avant le push, car les variables dans les args doivent être valuées
            // par la boucle enclosante.
            $boucle->set_args($this->parse_args());

            array_push($this->pile_boucle_courante, $boucle);

            //if (DEBUG_PARSER) { Analyse::echo_debug("Push boucle courante $boucle->nom\n", $this->pile_boucle_courante); }

            $boucle->ajouter($this->parse_content());

            // Skip remaining > TODO check >
            $this->skipto('>');

            return $boucle;
        }
        else if ($token_type === PexToken::FBS || $token_type === PexToken::FBR) {
            $this->controle_fermeture_boucle($token);

            array_pop($this->pile_boucle_courante);

            //if (DEBUG_PARSER) { Analyse::echo_debug("Pop boucle courante $token\n", $this->pile_boucle_courante); }

            return 'stop';
        }
        // BOUCLE CONDITIONNELLE
        else if ($token_type === PexToken::OBC) {
            $boucle = new BoucleConditionnelle(substr($token, 2));
            $this->skipto('>');

            array_push($this->pile_nom_boucles, $token);

            //if (DEBUG_PARSER) { Analyse::echo_debug("Push boucle conditionnelle $token\n", $this->pile_boucle_courante); }

            // Si
            $boucle->ajouter($this->parse_content());
            $this->skipto('>');

            array_push($this->pile_nom_boucles, '/'.$token);

            //if (	const ) { Analyse::echo_debug("Push SI boucle conditionnelle $token\n", $this->pile_boucle_courante); }

            // Sinon
            $boucle->ajouter($this->parse_content());
            $this->skipto('>');

            return $boucle;
        }
        else if ($token_type === PexToken::EBC) {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Controle fermeture SI: $token\n", $this->pile_boucle_courante); }

            $this->controle_fermeture_boucle($token);

            return 'stop';
        }
        else if ($token_type === PexToken::FBC) {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Controle fermeture ELSE: $token\n", $this->pile_boucle_courante);}

            $this->controle_fermeture_boucle($token);

            return 'stop';
        }
        // BOUCLE CONDITIONNELLE sur vaeriable
        else if ($token_type === PexToken::OBCV) {
        	$var = substr($token, 2);

        	// Ajouter la variable à la boucle enclosante
        	$this->add_var($var);

            $boucle = new BoucleConditionnelleVariable($var);
            $this->skipto('>');

            array_push($this->pile_nom_boucles, $token);

            //if (DEBUG_PARSER) { Analyse::echo_debug("Push boucle conditionnelle $token\n", $this->pile_boucle_courante); }

            // Si
            $boucle->ajouter($this->parse_content());
            $this->skipto('>');

            array_push($this->pile_nom_boucles, '/'.$token);

            //if (	const ) { Analyse::echo_debug("Push SI boucle conditionnelle $token\n", $this->pile_boucle_courante); }

            // Sinon
            $boucle->ajouter($this->parse_content());
            $this->skipto('>');

            return $boucle;
        }
        else if ($token_type === PexToken::EBCV) {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Controle fermeture SI: $token\n", $this->pile_boucle_courante); }

            $this->controle_fermeture_boucle($token);

            return 'stop';
        }
        else if ($token_type === PexToken::FBCV) {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Controle fermeture ELSE: $token\n", $this->pile_boucle_courante);}

            $this->controle_fermeture_boucle($token);

            return 'stop';
        }

        // Boucle <TEST_xxx>
        else if ($token_type === PexToken::OBT) {
            $boucle = new BoucleTest(substr($token, 5));

            // Parse args se fait avant le push, car les variables dans les args doivent être valuées
            // par la boucle enclosante.
            $boucle->set_args($this->parse_args());

            array_push($this->pile_nom_boucles, $token);

            //if (DEBUG_PARSER) { Analyse::echo_debug("Push boucle Test $boucle->nom\n", $this->pile_boucle_courante);}

            // Si
            $boucle->ajouter($this->parse_content());
            $this->skipto('>');

            array_push($this->pile_nom_boucles, '/' . $token);

            // Sinon
            $boucle->ajouter($this->parse_content());
            $this->skipto('>');

            return $boucle;
        }
        else if ($token_type === PexToken::EBT) {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Controle fermeture TEST SI: $token\n", $this->pile_boucle_courante);}

            $this->controle_fermeture_boucle($token);

            return 'stop';
        }
        else if ($token_type === PexToken::FBT) {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Controle fermeture TEST ELSE: $token\n", $this->pile_boucle_courante); }

            $this->controle_fermeture_boucle($token);

            return 'stop';
        }
        else if ($token_type === PexToken::OCM) {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Ouverture commentaire: $token\n", $this->pile_boucle_courante); }
        		$this->in_comment = true;

            return 'vide';
        }
        else if ($token_type === PexToken::FCM) {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Controle fermeture TEST ELSE: $token\n", $this->pile_boucle_courante); }
            $this->in_comment = false;
            $this->skipto('>');

            return 'vide';
        }
        else if ($token !== '') return new PexTexte($token);
        else return 'vide';
    }

    function skipto($val)
    {
        $ret = false;

        while ( ( ($tok = next($this->tokens)) !== false) && $tok != $val )
        {
            //if (DEBUG_PARSER) { Analyse::echo_debug("skipping $tok"); }
            $ret = $tok;
        }
    }

    function parse_content()
    {
        $contenu = new ContenuElement();

        $i = false;

        while (true) {
        		$token = next($this->tokens);

            // Done !
            if ($token === FALSE) {
                //if (DEBUG_PARSER) { Analyse::echo_debug("No more tokens.");}

	            	// Si on est encore dans un commentaire, failed !
	            	if ($this->in_comment) die('Erreur de syntaxe: un commentaire n\'a pas été fermé.');

	              // S'il reste des choses dans la pile des noms, des boucles n'ont pas été fermées correctement.
	              if (count($this->pile_nom_boucles) > 0) {
	                	die('Erreur de syntaxe: une ou plusieurs boucles n\'ont pas été fermées: '.implode(', ', $this->pile_nom_boucles));
	              }

	              break;
            }

            $res = $this->process_token($token);

            if ($res == 'vide') continue;
            else if ($res == 'stop') break;
            else $contenu->ajouter($res);
        }

        return $contenu;
    }
}
?>
