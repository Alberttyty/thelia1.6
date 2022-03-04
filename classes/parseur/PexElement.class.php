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

// Classe de base des éléments de template
abstract class PexElement{
    public abstract function imprimer();
    public abstract function evaluer(&$substitutions = array());
    public abstract function ajouter($data);
    public abstract function type();

    public function replace($substitutions, $texte){
        if (trim($texte) == '' /* || count($substitutions) == 0 */) return $texte;

        $val = &$texte;

        // Cas spécial des variables conditionnelles
        foreach(Parseur::$VARIABLES_CONDITIONNELLES as $varcond)
        {
	        if (isset($substitutions['#__VARCOND__'.$varcond.'__']))
	        {
	            $num_exp = $substitutions['#__VARCOND__'.$varcond.'__'] == '1' ? '1' : '2';

		 		$val = preg_replace('/#'.$varcond.'\[([^]]*)\]\[([^]]*)\]/', "\\$num_exp", $texte);
	        }
        }

        $subs = str_replace(array_keys($substitutions), array_values($substitutions), $val);

        // Traiter les variables de template s'il y en a
        if (strpos($subs, '#SET') !== false || strpos($subs, '#GET') !== false || strpos($subs, '#ENV') !== false || strpos($subs, '#SESSION') !== false)
        {
        	include_once(__DIR__.'/VariablesTemplate.class.php');

        	$subs = VariablesTemplate::analyser($subs);
        }

        return $subs;
    }
}

?>