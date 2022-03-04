<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
//require_once(dirname(realpath(__FILE__)) . '/../../../classes/filtres/FiltreBase.class.php');

class Filtretexte extends PluginsClassiques
{
		function Filtretexte()
		{
				$this->PluginsClassiques();
		}

		function post()
		{
       	global $res;

				// #FILTRE_Caracteres_speciaux
       	preg_match_all("`\#FILTRE_Caracteres_speciaux\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
		    $tab1 = [];
		    $tab2 = [];
		    for ($i=0; $i<count($cut[1]); $i++) {
		    		$search = array ('@[éèêë]@iu','@[àâä]@iu','@[îï]@iu','@[ûùü]@iu','@[ôö]@iu','@[ç]@i','@[&]@i');//,'@[^a-zA-Z0-9_]@'
		        $replace = array ('e','a','i','u','o','c','-');//,''
            $modif = preg_replace($search, $replace, $cut[1][$i]);
						$tab1[$i] = "#FILTRE_Caracteres_speciaux(|" . $cut[1][$i] . "|)";
		    		$tab2[$i] = $modif;
				}
				$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Url
        preg_match_all("`\#FILTRE_Url\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
		        $search = array ('@[éèêë]@iu','@[àâä]@iu','@[îï]@iu','@[ûùü]@iu','@[ôö]@iu','@[ç]@iu','@[ /_\']@i');//,'@[^a-zA-Z0-9_]@'
		        $replace = array ('e','a','i','u','o','c','-');//,''
            $modif = preg_replace($search, $replace, $cut[1][$i]);
            $modif = urlencode(strtolower($modif));
						$tab1[$i] = "#FILTRE_Url(|" . $cut[1][$i] . "|)";
		    		$tab2[$i] = $modif;
				}
				$res = str_replace($tab1, $tab2, $res);

        // #FILTRE_PtoBR
				preg_match_all("`\#FILTRE_PtoBR\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = preg_replace("@</p>@iS", "<br /><br />\n", $cut[1][$i]);
		        $modif = preg_replace("@<p\b.*>@UiS", "\n", $modif);
						$tab1[$i] = "#FILTRE_PtoBR(|" . $cut[1][$i] . "|)";
		    		$tab2[$i] = $modif;
				}
				$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Htmlentities
				preg_match_all("`\#FILTRE_Htmlentities\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = htmlentities($cut[1][$i]);
						$tab1[$i] = "#FILTRE_Htmlentities(|" . $cut[1][$i] . "|)";
				    $tab2[$i] = $modif;
				}
		    $res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Htmldecodeentities
        preg_match_all("`\#FILTRE_Htmldecodeentities\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = html_entity_decode($cut[1][$i],ENT_COMPAT,'UTF-8');
						// SECURITE => On ne décode pas " < > &
						$modif = htmlspecialchars($modif,ENT_COMPAT,'UTF-8');
						$tab1[$i] = "#FILTRE_Htmldecodeentities(|" . $cut[1][$i] . "|)";
				    $tab2[$i] = $modif;
				}
				$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Striptags
       	preg_match_all("`\#FILTRE_Striptags\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = strip_tags($cut[1][$i]);
						$modif = preg_replace("/(\r\n|\n|\r)/", " ", $modif);
						$tab1[$i] = "#FILTRE_Striptags(|" . $cut[1][$i] . "|)";
				    $tab2[$i] = $modif;
				}
	    	$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Stripretourligne
				preg_match_all("`\#FILTRE_Stripretourligne\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = preg_replace("/(\r\n|\n|\r)/", " ", $cut[1][$i]);
						$tab1[$i] = "#FILTRE_Stripretourligne(|" . $cut[1][$i] . "|)";
				    $tab2[$i] = $modif;
				}
				$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Strtoupper
        preg_match_all("`\#FILTRE_Strtoupper\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = mb_strtoupper($cut[1][$i],'UTF-8');
						$tab1[$i] = "#FILTRE_Strtoupper(|" . $cut[1][$i] . "|)";
				    $tab2[$i] = $modif;
				}
	    	$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Strtolower
       	preg_match_all("`\#FILTRE_Strtolower\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = mb_strtolower($cut[1][$i],'UTF-8');
		        $tab1[$i] = "#FILTRE_Strtolower(|" . $cut[1][$i] . "|)";
		        $tab2[$i] = trim($modif);
		    }
		    $res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Premmaj
		    preg_match_all("`\#FILTRE_Premmaj\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
		    $tab1 = [];
		    $tab2 = [];
		    for ($i=0; $i<count($cut[1]); $i++) {
						$modif = ucwords(mb_strtolower($cut[1][$i],'UTF-8'));
	        	$tab1[$i] = "#FILTRE_Premmaj(|" . $cut[1][$i] . "|)";
          	$tab2[$i] = $modif;
				}
	    	$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Remplace(subject,search,replace)
		    preg_match_all("`\#FILTRE_Remplace\(\|([^\|]*)\|\|([^\)]*)\|\|([^\)]*)\|\)`", $res, $cut);
		    $tab1 = [];
		    $tab2 = [];
	    	for ($i=0; $i<count($cut[1]); $i++) {
						$modif = str_replace($cut[2][$i],$cut[3][$i],$cut[1][$i]);
		        $tab1[$i] = "#FILTRE_Remplace(|" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "|)";
		        $tab2[$i] = $modif;
				}
	    	$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Preg_Remplace
        preg_match_all("`\#FILTRE_Preg_Remplace\(\|([^\|]*)\|\|([^\|]*)\|\|([^\|]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = preg_replace('`'.$cut[2][$i].'`',$cut[3][$i],$cut[1][$i]);
						$tab1[$i] = "#FILTRE_Preg_Remplace(|" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "|)";
				    $tab2[$i] = $modif;
				}
		    $res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Propre
        preg_match_all("`\#FILTRE_Propre\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for ($i=0; $i<count($cut[1]); $i++) {
						$modif = preg_replace('`\*(.+)\*`iU','<strong>$1</strong>',$cut[1][$i]);
			      $tab1[$i] = "#FILTRE_Propre(|" . $cut[1][$i] . "|)";
			      $tab2[$i] = $modif;
				}
	    	$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Brut
       	preg_match_all("`\#FILTRE_Brut\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
		    $tab1 = [];
		    $tab2 = [];
		    for ($i=0; $i<count($cut[1]); $i++) {
						$modif = preg_replace('`\*(.+)\*`iU','$1',$cut[1][$i]);
		        $tab1[$i] = "#FILTRE_Brut(|" . $cut[1][$i] . "|)";
			    	$tab2[$i] = $modif;
				}
		    $res = str_replace($tab1, $tab2, $res);

				// #FILTRE_Htmlspecialchars
		    preg_match_all("`\#FILTRE_Htmlspecialchars\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
		    $tab1 = [];
		    $tab2 = [];
		    for ($i=0; $i<count($cut[1]); $i++) {
						$modif = htmlspecialchars($cut[1][$i]);
		        $tab1[$i] = "#FILTRE_Htmlspecialchars(|" . $cut[1][$i] . "|)";
		        $tab2[$i] = $modif;
				}
		    $res = str_replace($tab1, $tab2, $res);

				// #FILTRE_textebrut
		    preg_match_all("`\#FILTRE_textebrut\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
		    $tab1 = [];
		    $tab2 = [];
		    for ($i=0; $i<count($cut[1]); $i++) {
						$u = $GLOBALS['meta']['pcre_u'];
          	$modif = html_entity_decode($cut[1][$i],ENT_COMPAT,'UTF-8');
           	$modif = preg_replace('/\s+/S'.$u, " ", $modif);
           	$modif = preg_replace("/<(p|br)( [^>]*)?".">/iS", "\n\n", $modif);
           	$modif = preg_replace("/^\n+/", "", $modif);
           	$modif = preg_replace("/\n+$/", "", $modif);
           	$modif = preg_replace("/\n +/", "\n", $modif);
           	$modif = strip_tags($modif);
           	$modif = preg_replace('/\"/', "'", $modif);
           	$modif = preg_replace("/(&nbsp;| )+/S", " ", $modif);
           	// nettoyer l'apostrophe curly qui pose probleme a certains rss-readers, lecteurs de mail...
           	$modif = str_replace("&#8220;","\"",$modif);
           	$modif = str_replace("&#8221;","\"",$modif);
           	$modif = str_replace("&#8216;","'",$modif);
           	$modif = str_replace("&#8217;","'",$modif);
           	$modif = str_replace("","'",$modif);
          	$modif = trim($modif);
			    	$tab1[$i] = "#FILTRE_textebrut(|" . $cut[1][$i] . "|)";
		        $tab2[$i] = $modif;
		    }
       	$res = str_replace($tab1, $tab2, $res);

				// #FILTRE_couper
       	preg_match_all("`\#FILTRE_Couper\(\|([^\|]*)\|\|([^\)]*)\|\)`", $res, $cut);
				$tab1 = [];
				$tab2 = [];
				for($i=0; $i<count($cut[1]); $i++) {
          	if(strlen($cut[1][$i]) <= $cut[2][$i]) $modif = $cut[1][$i];
          	else {
	             	$modif = mb_substr($cut[1][$i], 0, $cut[2][$i], 'UTF-8');
	             	$modif = substr($modif,0,strrpos($modif,' '))."...";
          	}
        		$tab1[$i] = "#FILTRE_Couper(|" . $cut[1][$i] . "||" . $cut[2][$i] . "|)";
						$tab2[$i] = $modif;
				}
				$res = str_replace($tab1, $tab2, $res);

        /*********Ne pas ajouter le trait vertical, uniquement les parenthèses, sinon pb pour certain titre contenant des traits verticaux*************/
       	preg_match_all("`\#FILTRE_Sansguillemet\(([^\)]*)\)`", $res, $cut);
		    $tab1 = [];
		    $tab2 = [];
		    for ($i=0; $i<count($cut[1]); $i++) {
						$modif = str_replace("'", "",$cut[1][$i]);
	          $modif = str_replace('"','',$cut[1][$i]);
						$tab1[$i] = "#FILTRE_Sansguillemet(" . $cut[1][$i] . ")";
			    	$tab2[$i] = $modif;
				}
		    $res = str_replace($tab1, $tab2, $res);
		}
}
?>
