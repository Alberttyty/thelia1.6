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

class Rubriquedesc extends BaseobjdescReecriture
{
		public $id;
		public $rubrique;

		const TABLE="rubriquedesc";
		public $table=self::TABLE;

		public $bddvars = ["id", "rubrique", "lang", "titre", "chapo", "description", "postscriptum"];

		public function __construct($rubrique = 0, $lang = false)
		{
				parent::__construct('rubrique', $rubrique, $lang);
		}

		public function charger($rubrique = null, $lang = null)
		{
				if ($rubrique != null) return $this->charger_desc($rubrique, $lang);
		}

		protected function clef_url_reecrite()
		{
				return "id_rubrique=$this->rubrique";
		}

		protected function texte_url_reecrite()
		{
				return $this->rubrique . "-" . $this->titre . ".html";
		}
}
?>
