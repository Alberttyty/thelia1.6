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
require_once __DIR__ . "/../fonctions/autoload.php";
require_once(__DIR__ . "/../fonctions/nettoyage.php");
require_once(__DIR__ . "/../fonctions/hierarchie.php");
require_once(__DIR__ . "/../fonctions/url.php");

abstract class BaseobjdescReecriture extends Baseobjdesc
{
		public function __construct($colonne, $fkey = 0, $lang = false)
		{
				// On a besoin de la langue demandée pour les opérations de réécriture futures
				if ($lang) $this->lang = $lang;
				parent::__construct($colonne, $fkey, $lang);
		}

		/**
		 * Cette methode doit retourner la clef de l'URL ré-ecrite, telle qu'elle estg utilisé
		 * dans la colonne 'param' de la table reecriture. Exemple pour un produit :
		 *
		 * id_produit=x&id_rubrique=y
		 */
		protected abstract function clef_url_reecrite();

		/**
		 * Cette méthode retourne l'URL ré-écrite telle qu'elle apparaît dans le back-office. Par exemple,
		 * pour un produit :
		 *
		 * idproduit-titrerubrique-titreptroduit.html
		 */
		protected abstract function texte_url_reecrite();

		public function getUrl()
		{
				if (Variable::lire("rewrite") != 0) {
						$reecriture = new Reecriture();

						if ($reecriture->charger_param($this->colonne, "&" . $this->clef_url_reecrite(), $this->lang, 1)) {
								return urlfond() ."/".$reecriture->url;
						}
				}

				return urlfond($this->colonne, htmlspecialchars($this->clef_url_reecrite()), true);
		}

		public function charger_reecriture()
		{
				$reecriture = new Reecriture();
				$reecriture->charger_param($this->colonne, "&" . $this->clef_url_reecrite(), $this->lang);

				return $reecriture;
		}

		public function reecrire($url = "")
		{
				$ret = 0;

				if ($url == "") $url = $this->texte_url_reecrite();

				$url = eregurl($url);

				$param = "&" . $this->clef_url_reecrite();
				$reecriture = new Reecriture();

				if (! $reecriture->charger($url)) {
						$reecriture->charger_param($this->colonne, $param, $this->lang, 1);

						if ($reecriture->url != $url) {
								$reecriture->actif = 0;
								$reecriture->maj();

								$reecriture = new Reecriture();

								$reecriture->fond = $this->colonne;
								$reecriture->url = $url;
								$reecriture->param = $param;
								$reecriture->lang = $this->lang;
								$reecriture->actif = 1;

								$ret = $reecriture->add();
						}
				}

				return $ret;
		}

		public function delete()
		{
				if ($reecriture = $this->charger_reecriture()) {
						$reecriture->actif = 0;
						$reecriture->maj();

						$reecriture_new = new Reecriture();
						$reecriture_new->url = $reecriture->url;
						$reecriture_new->fond = 'nexisteplus';
						$reecriture_new->param = $reecriture->param . '&ancienfond=' . $reecriture->fond;
						$reecriture_new->actif	 = 1;
						$reecriture_new->lang	 = $reecriture->lang;
						$reecriture_new->add();
				}

				parent::delete();
		}
}
?>
