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

class BoucleRepeter extends PexElement{
	public $nom;
	public $contenu;
	public $args;

    function __construct($nom)
    {
        $this->nom = $nom;
    }

    function type()
    {
        return PexToken::TYPE_BOUCLE_REPETER;
    }

    function set_args($args)
    {
        $this->args = $args;
    }

    function ajouter($data)
    {
        $this->contenu = $data;
    }

    function evaluer(&$substitutions = array())
    {
       if (DEBUG_EVAL) { Analyse::echo_debug("Evaluation boucle repeter $this->nom. RAW args: $this->args"); }

        $args = $this->replace($substitutions, $this->args);

        $debut = lireTag($args, "debut");
        $fin   = lireTag($args, "fin");
        $increment = lireTag($args, "increment");

		if ($debut == '') $debut = 1;
		if ($increment == '') $increment = 1;

        $val = '';

        if ($increment == 0) die("L'increment de la boucle REPETER_".$this->nom." doit être different de 0");

        for($idx = $debut, $count = 1; $idx <= $fin; $idx += $increment, $count++)
        {
        	$substitutions['#INDEX'] = $idx;
        	$substitutions['#__COMPTEUR__'] = $count;

            $val .= $this->contenu->evaluer($substitutions);
        }

        return $val;
    }

    function imprimer()
    {
        Analyse::echo_debug("[DEBUT REPETER $this->nom, args: ", $this->args, "]");
        $this->contenu->imprimer();
        Analyse::echo_debug("[FIN REPETER $this->nom]");
    }

}

?>