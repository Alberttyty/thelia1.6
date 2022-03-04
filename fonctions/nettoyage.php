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

// nettoyage d'url
function eregurl($url)
{
		$url = preg_replace("/\.html/i", '', $url);

		$url = html_entity_decode($url);
		$url = ereg_caracspec($url);
		$url = strip_tags($url);

		return $url/* . ".html"*/;
}

// nettoyage fichier
function eregfic($fichier)
{
		$fichier = ereg_caracspec($fichier);
		return preg_replace("/[^A-Za-z0-9._\-]/", "-", $fichier);
}

// remplacement des caractères spéciaux + accents
function ereg_caracspec($chaine)
{
    $chaine = trim($chaine);

    if (function_exists('mb_strtolower')) $chaine = mb_strtolower($chaine, 'UTF-8');
    else $chaine = strtolower($chaine);

    $chaine = supprAccent($chaine);

    $chaine = str_replace(
    		[':', ';', ',', '°'],
        ['-', '-', '-', '-'],
        $chaine
    );

    $chaine = str_replace("(", "", $chaine);
    $chaine = str_replace(")", "", $chaine);
    $chaine = str_replace("/", "-", $chaine);
    $chaine = str_replace(" ", "-", $chaine);
    $chaine = str_replace("'", "-", $chaine);
    $chaine = str_replace("\"", "-", $chaine);
    $chaine = str_replace("&", "-", $chaine);
    $chaine = str_replace("?", "", $chaine);
    $chaine = str_replace("*", "-", $chaine);
    $chaine = str_replace(".", "", $chaine);
    $chaine = str_replace("!", "", $chaine);
    $chaine = str_replace("’", "", $chaine);
    $chaine = str_replace("+", "-", $chaine);
    $chaine = str_replace("%", "", $chaine);
    $chaine = str_replace("²", "2", $chaine);
    $chaine = str_replace("³", "3", $chaine);
    $chaine = str_replace("œ", "oe", $chaine);
    $chaine = str_replace("½", "1-2", $chaine);
    $chaine = str_replace("¼", "1-4", $chaine);
    $chaine = str_replace("¾ ", "3-4", $chaine);
		/*
    Brise les chaines de caractères multibytes

		$chaine = str_replace(chr(39), "-", $chaine);
		$chaine = str_replace(chr(234), "e", $chaine);
		$chaine = str_replace(chr(128), "E", $chaine);
		$chaine = str_replace(chr(226), "E", $chaine);
		$chaine = str_replace(chr(146), "-", $chaine);
		$chaine = str_replace(chr(150), "-", $chaine);
		$chaine = str_replace(chr(151), "-", $chaine);
		$chaine = str_replace(chr(153), "", $chaine);
		$chaine = str_replace(chr(169), "", $chaine);
		$chaine = str_replace(chr(174), "", $chaine);
		*/
		return $chaine;
}

// suppression d'accent
function supprAccent($texte)
{
		$texte = str_replace(
				[
					 	'à', 'â', 'ä', 'á', 'ã', 'å',
			      'î', 'ï', 'ì', 'í',
			      'ô', 'ö', 'ò', 'ó', 'õ', 'ø',
			      'ù', 'û', 'ü', 'ú',
			      'é', 'è', 'ê', 'ë',
			      'ç', 'ÿ', 'ñ', 'ý'
			  ],
				[
						'a', 'a', 'a', 'a', 'a', 'a',
						'i', 'i', 'i', 'i',
						'o', 'o', 'o', 'o', 'o', 'o',
						'u', 'u', 'u', 'u',
						'e', 'e', 'e', 'e',
						'c', 'y', 'n', 'y'
				],
				$texte
		);

	  $texte = str_replace(
				[
						'À', 'Â', 'Ä', 'Á', 'Ã', 'Å',
						'Î', 'Ï', 'Ì', 'Í',
						'Ô', 'Ö', 'Ò', 'Ó', 'Õ', 'Ø',
						'Ù', 'Û', 'Ü', 'Ú',
						'É', 'È', 'Ê', 'Ë',
						'Ç', 'Ÿ', 'Ñ', 'Ý',
				],
				[
						'A', 'A', 'A', 'A', 'A', 'A',
						'I', 'I', 'I', 'I',
						'O', 'O', 'O', 'O', 'O', 'O',
						'U', 'U', 'U', 'U',
						'E', 'E', 'E', 'E',
						'C', 'Y', 'N', 'Y',
				],
				$texte
		);

		return $texte;
}
?>
