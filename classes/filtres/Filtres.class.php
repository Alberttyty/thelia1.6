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
/*	    along with this program.  If not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/

require_once __DIR__ . "/../../fonctions/autoload.php";

class Filtres {

	/*
	 * Les filtres par défaut, liste de couples : suffixe_du nom_de_filtre => suffixe_nom_de_classe
	 *
	 */
	static $FILTRES_DEFAUT = array(
		'vide' => 'Vide',
		'vrai' => 'Vrai',
		'faux' => 'Faux',
		'egalite' => 'Egalite',
		'different' => 'Different',
		'min' => 'Fonction',
		'maj' => 'Fonction',
		'sanstags' => 'Fonction',
		'modulo' => 'ModuloInt',
        'supegal' => 'Supegal'
	);

	/*
	 * Les filtres utilisateurs: instance d'une classe qui implémente FiltreBase. Enrichi par la méthode enregistrer_filtre().
	 *
	 * Exemple:
	 *
	 *	class FiltreExemple extends FiltreBase
	 *	{
 	 *		public function __construct()
	 *		{
	 *			parent::__construct("`\#FILTRE_exemple\(([^\|]*)\)`");
	 *		}
	 *
	 *		public function calcule($match)
	 *		{
	 *			return 'Filtré:'.$match[1];
	 *		}
	 *
	 *	}
	*/

	private static $FILTRES_UTILISATEUR = array();

	/*
	 * Effectuer les subtitutions de tous les filtres
	 *
	 * @param FiltreBase $filtre une instance de classe qui implémente FiltreBase
	 */
	public static function exec(&$texte)
	{
		foreach(self::$FILTRES_DEFAUT as $nom_filtre => $class)
		{
			if(strstr($texte, "#FILTRE_$nom_filtre")) {

				$classname = "Filtre$class";

				require_once("$classname.class.php");

				$f = new $classname($nom_filtre);

				$f->exec($texte);
			}
		}

		// Les filtres présents dans client/plugins
		$filtres_utilisateur = ActionsModules::instance()->lister(Modules::FILTRE, true);

		// Filtres utilisateur
		foreach($filtres_utilisateur as $filtre)
		{
			try {
				$instance = ActionsModules::instance()->instancier($filtre->nom);

				$instance->exec($texte);

			} catch (Exception $e) {}
		}

		// Les filtres explicitement enregistrés par enregistrer_filtres();
		foreach(self::$FILTRES_UTILISATEUR as $filtre)
		{
			$filtre->exec($texte);
		}
	}

	/*
	 * Enregistrer un filtre utilisateur.
	 *
	 * @param FiltreBase $filtre une instance de classe qui implémente FiltreBase
	 */
	public static function enregistrer_filtre($filtre)
	{
		if ($filtre instanceof FiltreBase) {
			self::$FILTRES_UTILISATEUR[] = $filtre;
		}
		else {
			die(class_name($filtre) . " doit implémenter la classe FiltreBase.");
		}
	}
}
?>