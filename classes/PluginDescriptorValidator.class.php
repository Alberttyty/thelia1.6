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

class PluginDescriptorValidator {

	private $xsd;

	public function __construct($xsd) {

		// Enable user error handling
		libxml_use_internal_errors(true);

		$this->xsd = $xsd;
	}

	private function libxml_format_error($error)
	{
		$return = "<br/>\n";

		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "<b>Warning $error->code</b>: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "<b>Erreur $error->code</b>: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "<b>Erreur fatale $error->code</b>: ";
				break;
		}

		$return .= trim($error->message);

		if ($error->file) {
			$return .=    " fichier <b>$error->file</b>";
		}

		$return .= " ligne <b>$error->line</b>\n";

		return $return;
	}

	private function libxml_get_errors() {

		$return = "";

		$errors = libxml_get_errors();

		foreach ($errors as $error) {
			$return .= $this->libxml_format_error($error);
		}

		libxml_clear_errors();

		return $return;
	}

	private function add_text_node($dom, &$tonode, $tagname, $value) {
		$node = $dom->createElement($tagname);
		$text = $dom->createTextNode($value);
		$node->appendChild($text);
		$tonode->appendChild($node);
	}

	private function get_node_text($dom, $nodename, $defaut = '') {

		$value = $defaut;

		$nodes = $dom->getElementsByTagName($nodename);

		if ($nodes->length > 0) {
			$value = $nodes->item(0)->nodeValue;
		}

		return $value;
	}

	private function migration($plugin_xml) {

		$dom_old = new DOMDocument();

		if ($dom_old->load($plugin_xml)) {

			$save_plugin_xml = $plugin_xml . ".save";

			@unlink($save_plugin_xml);

			$dom_new = new DOMDocument('1.0', 'UTF-8');

			$commentaire = $dom_new->createComment(
				trad("Migration automatique depuis le %s original, sauvegardé dans %s", 'admin', basename($plugin_xml), basename($save_plugin_xml))
			);

			$dom_new->appendChild($commentaire);

			$plugin = $dom_new->createElement('plugin');

			$description = $dom_new->createElement('descriptif');

			$attribute = $dom_new->createAttribute('lang');
			$attribute->value = 'fr';

			$description->appendChild($attribute);

			$this->add_text_node($dom_new, $description, 'titre', $this->get_node_text($dom_old, 'nom'));
			$this->add_text_node($dom_new, $description, 'chapo', $this->get_node_text($dom_old, 'description'));
			$this->add_text_node($dom_new, $description, 'description', '');
			$this->add_text_node($dom_new, $description, 'postscriptum', '');

			$plugin->appendChild($description);

			// Version
			$this->add_text_node($dom_new, $plugin, 'version', $this->get_node_text($dom_old, 'version', 'Indéfinie'));

			// Auteur
			$auteur = $dom_new->createElement('auteur');

			$this->add_text_node($dom_new, $auteur, 'nom', $this->get_node_text($dom_old, 'auteur'));
			$this->add_text_node($dom_new, $auteur, 'societe', '');
			$this->add_text_node($dom_new, $auteur, 'email', '');
			$this->add_text_node($dom_new, $auteur, 'web', '');

			$plugin->appendChild($auteur);

			// Type
			$this->add_text_node($dom_new, $plugin, 'type', $this->get_node_text($dom_old, 'type', 'classique'));

			// Prerequis (vide).
			$prerequis = $dom_new->createElement('prerequis');
			$plugin->appendChild($prerequis);

			// Version Thelia mini
			$this->add_text_node($dom_new, $plugin, 'thelia', $this->get_node_text($dom_old, 'thelia', '1.4.0'));

			// Etat
			$this->add_text_node($dom_new, $plugin, 'etat', $this->get_node_text($dom_old, 'etat', 'Indéfini'));

			// Documentation
			$this->add_text_node($dom_new, $plugin, 'documentation', $this->get_node_text($dom_old, 'documentation', ''));

			// Url de mise à jour (vide...)
			$this->add_text_node($dom_new, $plugin, 'urlmiseajour', '');

			if (rename($plugin_xml, $save_plugin_xml)) {

				$dom_new->appendChild($plugin);

				$dom_new->formatOutput = true;

				$dom_new->save($plugin_xml);
			}
			else {
				throw new TheliaException(
					"Ne peut sauvegarder le fichier plugin.xml original",
					TheliaException::MODULE_ECHEC_MIGRATION_DESCRIPTEUR
				);
			}
		}
		else {
			throw new TheliaException(
				trad("Le plugin ne peut être activé. Echec de la validation du descripteur %s: %s", 'admin', $plugin_xml, $this->libxml_get_errors()),
				TheliaException::MODULE_ECHEC_VALIDATION_DESCRIPTEUR
			);
		}
	}

	public function validate($plugin_xml) {

		// Validation d'un doc XML
		$dom = new DOMDocument();

		if ($dom->load($plugin_xml)) {

			// Migration si nécessaire ? si l'élément prerequis est présent, c'est le nouveau format
			$element_descriptif = $dom->getElementsByTagName("prerequis");

			if ($element_descriptif->length == 0) {
				$this->migration($plugin_xml);

				// Rechercher le plugin.xml migré
				if (! $dom->load($plugin_xml)) {
					throw new Exception("Ne peut charger le $plugin_xml migré:".$this->libxml_get_errors());
				}
			}

			if (! $dom->schemaValidate($this->xsd)) {

				throw new Exception("Ne peut valider $plugin_xml:".$this->libxml_get_errors());
			}
		}
		else {
			throw new Exception("Ne peut charger $plugin_xml:".$this->libxml_get_errors());
		}
	}
}
?>