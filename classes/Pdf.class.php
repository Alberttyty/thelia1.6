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
/*
 * Encapsulation de la génération de fichiers PDF
 */
require_once __DIR__ . "/../fonctions/autoload.php";
require_once(__DIR__ . "/../lib/html2pdf/html2pdf.class.php");

class Pdf
{
		// Valeurs par défaut
		const SENS = 'P'; // Orientation: P=portrait, L=landscape
		const FORMAT = 'A4'; // A5, etc.

		const LANG = 'fr'; // en, it, etc.

		const UNICODE = true;
		const ENCODING = 'UTF-8';
		const MARGIN = 0;

		private static $instance = false;

		private function __construct()
		{}

		public static function instance()
		{
				if (! $instance) self::$instance = new Pdf();
				return self::$instance;
		}

		public function generer(
				$html,
				$nom_fichier = '', $dest = 'I',
				$sens = self::SENS, $format = self::FORMAT, $margin = array(self::MARGIN, self::MARGIN, self::MARGIN, self::MARGIN)
		)
		{
		    try {
		        $html2pdf = new HTML2PDF($sens, $format, self::LANG, self::UNICODE, self::ENCODING, $margin);
		        $html2pdf->pdf->SetDisplayMode('real');
		        $html2pdf->writeHTML($html);
		        $html2pdf->Output($nom_fichier, $dest);
		    }
				catch(Exception $e) {
		        die("Echec de création du document PDF: $e");
		    }
		}
}
?>
