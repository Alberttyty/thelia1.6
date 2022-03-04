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

class Contenudesc extends BaseobjdescReecriture
{
		public $id;
		public $contenu;

		const TABLE = "contenudesc";

		public $table = self::TABLE;

		public $bddvars = array("id", "contenu", "titre", "chapo", "description", "lang", "postscriptum");

		public function __construct($contenu = 0, $lang = false)
		{
				parent::__construct('contenu', $contenu, $lang);
		}

		public function charger($contenu = null, $lang = null)
		{
				if ($contenu != null) return parent::charger_desc($contenu, $lang);
		}

		protected function clef_url_reecrite()
		{
				$contenu = new Contenu($this->contenu);
				return self::calculer_clef_url_reecrite($contenu->id, $contenu->dossier);
		}

		protected function texte_url_reecrite()
		{
				$contenu = new Contenu($this->contenu);
				$dossierdesc = new Dossierdesc($contenu->dossier, $this->lang);

				return $contenu->id . "-" . $dossierdesc->titre . "-" . $this->titre . ".html";
		}


		public static function calculer_clef_url_reecrite($id_contenu, $id_dossier)
		{
				return "id_contenu=$id_contenu&id_dossier=$id_dossier";
		}

}
?>
