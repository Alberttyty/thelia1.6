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

class BoucleConditionnelle extends PexElement{
	public $nom;
	public $contenu;

	public $valeur;

    function __construct($nom)
    {
        $this->nom = $nom;
        $this->contenu = array();
    }

    function type()
    {
        return PexToken::TYPE_BOUCLE_COND;
    }

    function evaluer(&$substitutions = array())
    {
       // Evaluer la boucle. Le scope des variables positionnes par cette boucle
        // n'est pas propage au contenu des boucles imbriquées
        if (DEBUG_EVAL) { Analyse::echo_debug("Eval boucle conditionnelle $this->nom"); }

        $si = $this->contenu[0]->evaluer($substitutions);

        if (DEBUG_EVAL) { Analyse::echo_debug("Eval boucle conditionnelle $this->nom: ", $si); }

        // Trouver la boucle concernée, ou la première boucle si aucun nom de boucle ne matche
        $premiere_boucle = false;
        $boucle_test = false;
        $nb_boucles = 0;

        foreach($this->contenu[0]->elements as &$element)
        {
            if (DEBUG_EVAL) { Analyse::echo_debug("checking element nom='$element->nom', type=".$element->type(),':', $element); }

            if ($element->type() == PexToken::TYPE_BOUCLE_SIMPLE)
            {
                $nb_boucles++;

                if ($premiere_boucle === false) $premiere_boucle = &$element;

                if ($element->nom == $this->nom)
                {
                    if (DEBUG_EVAL) { Analyse::echo_debug("Boucle 'si' trouve pour $this->nom"); }

                    $boucle_test = &$element;

                    break;
                }
            }
        }

        // Par defaut, la boucle est vide.
        $est_vide = true;

        // Aucune boucle trouvée ? On evalue le texte de la condition 'si'
        if ($nb_boucles == 0)
        {
            $est_vide = trim($si) != '';
        }
        // Une boucle ? On regarde si elle est vide
        else if ($boucle_test === false)
        {
            if ($premiere_boucle === false)
            {
                die ("Boucle conditionnelle T_$this->nom: boucle THELIA_$this->nom non trouvée.");
            }
            else
            {
                $est_vide = $premiere_boucle->est_vide;
            }
        }
        else
        {
            $est_vide = $boucle_test->est_vide;
        }

        if (DEBUG_EVAL) { Analyse::echo_debug("boucle $this->nom ", $est_vide ? " Vide" : " Non vide"); }

        if ($est_vide)
        {
        	if (DEBUG_EVAL) { Analyse::echo_debug("Eval expression 'vide'"); }

            return $this->contenu[1]->evaluer($substitutions);
        }
        else
        {
        	if (DEBUG_EVAL) { Analyse::echo_debug("Retourne expression 'non vide'"); }

            return $si;
        }
    }

    function ajouter($data)
    {
        if (DEBUG_EVAL) { Analyse::echo_debug("BoucleConditionnelle ajout:", $data); }

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