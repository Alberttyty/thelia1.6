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
/**
 * Administration des devises depuis le back office
 *
 * Ce singleton permet de gérer la manipulation des devises depuis l'admin Thelia.
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 * @version $Id$
 */

class ActionsAdminDevises extends ActionsDevises {

	private static $instance = false;

	private function __construct() {
		parent::__construct();
	}

	/**
	 * Cette classe est un singleton
	 * @return ActionsAdminDevises une instance de ActionsAdminDevises
	 */
	public static function instance() {
		if (self::$instance === false) self::$instance = new ActionsAdminDevises();

		return self::$instance;
	}

	/**
	 * Modifier une devise existante
	 *
	 * @param int $id
	 * @param string $nom
	 * @param float $taux
	 * @param string $symbole
	 * @param string $code
	 * @param int $defaut 0 ou 1
	 */
	public function modifier($id, $nom, $taux, $symbole, $code, $defaut) {
		$devise = new Devise();

		if ($devise->charger($id)) {

			$devise->nom = $nom;
			$devise->taux = $taux;
			$devise->symbole = $symbole;
			$devise->code = $code;
			$devise->defaut = $defaut;

			$devise->maj();

			ActionsModules::instance()->appel_module("moddevise", $devise);
		}
	}

	/**
	 * Ajouter une nouvelle devise
	 *
	 * @param string $nom
	 * @param float $taux
	 * @param string $symbole
	 * @param string $code
	 */
	public function ajouter($nom, $taux, $symbole, $code) {
		$devise = new Devise();

		$devise->nom = $nom;
		$devise->taux = $taux;
		$devise->symbole = $symbole;
		$devise->code = $code;

		$devise->add();

		ActionsModules::instance()->appel_module("ajoutdevise", $devise);
	}

	/**
	 * Supprimer une devise existante
	 */
	public function supprimer($id) {
		$devise = new Devise();

		if ($devise->charger_id($id)) {
			$devise->delete();

			ActionsModules::instance()->appel_module("suppdevise", $devise);
		}
	}

	/**
	 * Mettre à jour les taux de conversions par rapport à l'Euro
	 */
	public function refresh(){

		$file_contents = file_get_contents('http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml');

		$devise = new Devise();

		if ($file_contents && $sxe = new SimpleXMLElement($file_contents)) {

			foreach ($sxe->Cube[0]->Cube[0]->Cube as $last)
			{
				$devise->query("UPDATE $devise->table SET  taux='".$devise->escape_string($last["rate"])."' WHERE code='".$devise->escape_string($last["currency"])."'");
			}
		}
	}

	/**
	 * Retourne une liste des devises
	 */
	public function lister() {

		$devise = new Devise();

		return $devise->query_liste("select * from $devise->table", "Devise");
	}
}
?>