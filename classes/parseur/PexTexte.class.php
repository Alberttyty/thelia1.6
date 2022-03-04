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

class PexTexte extends PexElement
{
		public $texte;

    function __construct(&$texte)
    {
        $this->texte = $texte;
    }

    function type()
    {
        return PexToken::TYPE_TEXTE;
    }

    function ajouter($texte)
    {
        $this->texte .= $texte;
    }

    function evaluer(&$substitutions = [])
    {
    		if (DEBUG_EVAL) Analyse::echo_debug("Eval texte '$this->texte'");
      	return $this->replace($substitutions, $this->texte);
    }

    function imprimer()
    {
        Analyse::echo_debug($this->texte);
    }
}

?>
