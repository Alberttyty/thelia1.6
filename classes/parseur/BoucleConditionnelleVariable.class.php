<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) OpenStudio		                                             */
/*		email : thelia@openstudio.fr		        	                          	 */
/*      web : http://www.openstudio.fr						   						 */
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
/*	 Ê Êalong with this program. ÊIf not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/

require_once __DIR__ . "/../../fonctions/autoload.php";

class BoucleConditionnelleVariable extends PexElement{
	public $nom;
	public $contenu;

    function __construct($nom)
    {
        $this->nom = $nom;
        $this->contenu = array();
    }

    function type()
    {
        return PexToken::TYPE_BOUCLE_COND_VARIABLE;
    }

    function evaluer(&$substitutions = array())
    {
		$idx = isset($substitutions['#'.$this->nom]) && $substitutions['#'.$this->nom] != '' ? 0 : 1;

		return $this->contenu[$idx]->evaluer($substitutions);
    }

    function ajouter($data)
    {
        if (DEBUG_EVAL) { Analyse::echo_debug("BoucleConditionnelleVariable ajout:", $data); }

        $this->contenu[] = $data;
    }

    function imprimer()
    {
        Analyse::echo_debug("[SI $this->nom]");
        if ($this->contenu[0]) $this->contenu[0]->imprimer();
        Analyse::echo_debug("[SINON $this->nom]");
        if ($this->contenu[1]) $this->contenu[1]->imprimer();
        Analyse::echo_debug("[FINSI $this->nom]");
    }
}
?>