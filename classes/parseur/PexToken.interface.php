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

require_once __DIR__ . "/../../fonctions/autoload.php";

// Tokens and separators
// ---------------------
interface PexToken{
	// Token types
	const OBS = 1;
	const FBS = 2;
	const OBC = 3;
	const EBC = 4;
	const FBC = 5;
	const TXT = 7;
	const OBT = 8;
	const EBT = 9;
	const FBT = 10;
	const OCM = 11;
	const FCM = 12;
	const OBR = 13;
	const FBR = 14;
	const OBCV = 15;
	const EBCV = 16;
	const FBCV = 17;

	// Element types
	const TYPE_BOUCLE_SIMPLE = 1;
	const TYPE_BOUCLE_COND = 2;
	const TYPE_BOUCLE_TEST = 21;
	const TYPE_TEXTE = 3;
	const TYPE_CONTENU = 4;
    const TYPE_BOUCLE_REPETER = 5;
	const TYPE_BOUCLE_COND_VARIABLE = 6;

	// Separateurs
	const ITER_SEP   = "\x01";
	const COUPLE_SEP = "\x02";
	const ASSIGN_SEP = "\x03";
	const START_MARK = "\x04";
}

?>