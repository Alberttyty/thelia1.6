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

class ContenuElement extends PexElement
{
		public $elements;
		public $idx;
		public $last_type;

    function __construct()
    {
        $this->elements = [];
        $this->idx = 0;
        $this->last_type = -1;
    }

    function type()
    {
        return PexToken::TYPE_CONTENU;
    }

    function ajouter($element)
    {
        // Merge subsequent text elements
        //if (DEBUG_PARSER) { Analyse::echo_debug("Contenu ajouter: idx=".$this->idx.", last_type=".$this->last_type.", element type=".$element->type());}

        if ($this->idx > 0
            &&
            $this->last_type == PexToken::TYPE_TEXTE
            &&
            $element->type() == PexToken::TYPE_TEXTE)
        {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Contenu append: "); $element->imprimer();}
            $this->elements[$this->idx-1]->ajouter($element->texte);
        }
        else {
            //if (DEBUG_PARSER) { Analyse::echo_debug("Contenu ajout:"); $element->imprimer(); }
            $this->elements[] = $element;
            $this->last_type = $element->type();
            //if (DEBUG_PARSER) { Analyse::echo_debug("Après ajout: last_type=".$this->last_type); }
            $this->idx++;
        }
    }

    function evaluer(&$substitutions = [])
    {
	    	if (DEBUG_EVAL) Analyse::echo_debug("Eval contenu $this->idx");

	    	$val = '';

	      foreach($this->elements as $element) {
	          if (DEBUG_EVAL) Analyse::echo_debug("CONT:eval ". $element->type()); $element->imprimer();
	          $val .= $element->evaluer($substitutions);
	      }

	      return $val;
    }

    function imprimer()
    {
        foreach($this->elements as $element) {
            $element->imprimer();
        }
    }
}
?>
