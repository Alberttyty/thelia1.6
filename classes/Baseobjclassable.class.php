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

class Baseobjclassable extends Baseobj
{
		private $nom_colonnes = false;
		private $methode_module = false;

		public function __construct($nom_colonnes = false, $methode_module = false)
		{
				parent::__construct();

				$this->nom_colonnes = $nom_colonnes;
				$this->methode_module = $methode_module;
		}

		private function construire_where_clause($avec_and = true)
		{
				if ($this->nom_colonnes == false) $where_colonne = '1 and ';
				else if (is_array($this->nom_colonnes)) {
						$where_colonne = "";

						foreach($this->nom_colonnes as $colonne) {
								$where_colonne .= "$colonne=".intval($this->$colonne)." and ";
						}
				}
				else {
						$simple_colonne = $this->nom_colonnes;
						$where_colonne = "$simple_colonne=".intval($this->$simple_colonne)." and ";
				}

				if (! $avec_and) $where_colonne = rtrim($where_colonne, " and ");

				return $where_colonne;
		}

		public function prochain_classement()
		{
				$classement = 1;

				$where_colonne = $this->construire_where_clause(false);

				$query = "select max(classement) as max from ".$this->table." where $where_colonne";

				$resul = $this->query($query);

				if ($resul) $classement = 1 + $this->get_result($resul);

				return $classement;
		}

		public function modifier_classement($id, $classement)
		{
				if ($this->charger_id($id)) {
						$where_colonne = $this->construire_where_clause();

						if ($classement > $this->classement) {
								$between = $this->classement." and $classement";
								$delta = -1;
						}
						else {
								$between = "$classement and ".$this->classement;
								$delta = +1;
						}

						$query = "select * from ".$this->table." where $where_colonne classement BETWEEN $between";

						$resul = $this->query($query);

						while ($resul && $row = $this->fetch_object($resul, get_class($this))) {
								$row->classement += $delta;
								$row->maj();
						}

						$this->classement = $classement;
						$this->maj();

						if ($this->methode_module != false) ActionsModules::instance()->appel_module($this->methode_module, $this);
				}
		}

		public function changer_classement($id, $sens)
		{
				if ($this->charger_id($id)) {
						$where_colonne = $this->construire_where_clause();

						if ($sens == "M") $req = " < " . $this->classement . " order by classement desc";
						else if ($sens == "D") $req = " > " . $this->classement . " order by classement";
						else return;

						$res = $this->query("select id, classement from ".$this->table." where $where_colonne classement $req limit 0,1");

						if ($res && $this->num_rows($res) > 0) {
								$repl_id = $this->get_result($res, 0, 0);
								$repl_classement = $this->get_result($res, 0, 1);

								$res = $this->query("update ".$this->table." set classement = ".intval($repl_classement)." where id=".$this->id);

								if ($res) $this->query("update ".$this->table." set classement = ".intval($this->classement)." where id = $repl_id");
						}
				}
		}

		public function before_add()
		{
				$this->classement = $this->prochain_classement();
		}

		public function before_delete()
		{
				$where_colonne = $this->construire_where_clause();

				// Mettre a jour le classement
				$query = "update ".$this->table." set classement=(classement-1) where $where_colonne classement > ".$this->classement;

				$this->query($query);
		}

		public function add()
		{
				$this->before_add();
				return parent::add();
		}

		public function delete()
		{
				$this->before_delete();
				parent::delete();
		}
}
