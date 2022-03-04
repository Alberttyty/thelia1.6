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

require_once __DIR__ . "/../../fonctions/autoload.php";

class BoucleTest extends PexElement{
	public $nom;
	public $contenu;
	public $args;

    function __construct($nom)
    {
        $this->nom = $nom;
        $this->contenu = array();
    }

    function type()
    {
        return PexToken::TYPE_BOUCLE_TEST;
    }

    function set_args($args)
    {
        $this->args = $args;
    }

    function ajouter($data)
    {
        $this->contenu[] = $data;
    }

    function evaluer(&$substitutions = array())
    {
       if (DEBUG_EVAL) { Analyse::echo_debug("Evaluation boucle test $this->nom. RAW args: $this->args"); }

        $args = $this->replace($substitutions, $this->args);

        $var  = lireTag($args, "variable");
        if ($var == '') $var = lireTag($args, "var");

        $test = lireTag($args, "test");
        $val  = lireTag($args, "valeur");
        if ($val == '') $val = lireTag($args, "val");

        if (DEBUG_EVAL) { Analyse::echo_debug("Boucle test: args='$args', var='$var', test='$test', val='$val'"); }

        $vrai = false;

        switch(strtolower($test))
        {
            case "vide" :
                $vrai = trim($var) == '';
            break;

            case "nonvide" :
                $vrai = trim($var) != '';
            break;

            case "egal" :
                $vrai = ($var == $val);
            break;

            case "different" :
                $vrai = ($var != $val);
            break;

            case "superieur" :
                $vrai = ($var > $val);
            break;

            case "superieurouegal" :
                $vrai = ($var >= $val);
            break;

            case "inferieur" :
                $vrai = ($var < $val);
            break;

            case "inferieurouegal" :
                $vrai = ($var <= $val);
            break;

            case "dansliste" :
            	$sep = lireTag($args, "separateur");

            	if (empty($sep)) $sep = ",";

                $vrai = in_array($var, explode($sep, $val));
            break;

        	case "contient" :
        		$vrai = strstr($var, $val) !== false;
        	break;

        	// Contribution de asturyan
			case "modulo" :
        		$val = explode(",", $val);

                $vrai = ($var % $val[0] == $val[1]);
            break;

            default:
                die("L'argument 'test' de la boucle $this->nom est manquant ou inconnu: '$test'");
            break;
        }

        if ($vrai)
        {
            return $this->contenu[0]->evaluer($substitutions);
        }
        else
        {
            return $this->contenu[1]->evaluer($substitutions);
        }
    }

    function imprimer()
    {
        Analyse::echo_debug("[TEST_VRAI $this->nom $args]");
        if ($this->contenu[0]) $this->contenu[0]->imprimer();
        Analyse::echo_debug("[TEST_FAUX $this->nom]");
        if ($this->contenu[1]) $this->contenu[1]->imprimer();
        Analyse::echo_debug("[TEST_FIN $this->nom]");
    }

}

?>