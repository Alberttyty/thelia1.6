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
require_once __DIR__ . "/../fonctions/autoload.php";

class Variable extends Baseobj
{
		public $id;
		public $nom;
		public $valeur;
	 	public $protege;
	 	public $cache;

		const TABLE = "variable";
		public $table = self::TABLE;

		public $bddvars = ["id", "nom", "valeur", "protege", "cache"];

		function __construct($nom = "")
		{
				parent::__construct();
				if ($nom != "") $this->charger($nom);
		}

		function charger($nom = null, $var2 = null)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE nom=\"$nom\"");
		}

		function charger_id($id)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		/**
		 * Pour obtenir la valeur d'une variable en un seul appel: Variable::lire("nomvariable")
		 */
		static $_cache = [];
		public static function lire($nom, $defaut = "")
		{
				if (! isset(self::$_cache[$nom])) {
						$var = new Variable($nom);
						self::$_cache[$nom] = empty($var->id) ? $defaut : $var->valeur;
				}

				return self::$_cache[$nom];
		}

		public function add()
		{
				unset(self::$_cache[$this->nom]);
				return parent::add();
		}

		public function maj()
		{
				unset(self::$_cache[$this->nom]);
				return parent::maj();
		}

		public function delete() {
				unset(self::$_cache[$this->nom]);
				parent::delete();
		}

		/*
		 * Pour mettre a jour la valeur d'une variable en un seul appel: Variable::ecrire("nomvariable", valeur)
		 */
		public static function ecrire($nom, $valeur, $creer_si_inexistante = false, $protege = 1, $cache = 1)
		{
				$var = new Variable($nom);

				if ($creer_si_inexistante && ! $var->charger($nom)) {
						$var->nom = $nom;
						$var->valeur = $valeur;
						$var->protege = $protege;
						$var->cache = $cache;

						$var->add();
				}
				else {
						$var->valeur = $valeur;
						$var->maj();
				}
		}
}
?>
