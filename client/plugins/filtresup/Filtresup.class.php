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
	
class Filtresup extends PluginsClassiques {

	function Filtresup() {
		$this->PluginsClassiques();	
	}
		
	function init() {
	}

	function destroy() {
	}		

	function post() {
			
    	global $res;
		preg_match_all("`\#FILTRE_sup\(([^\|]*)\|\|([^\|]*)\|\|([^\|]*)\|\|([^\)]*)\)`", $res, $cut);
        $tab1 = "";
		$tab2 = "";
		
		for($i=0; $i<count($cut[2]); $i++) {
			if($cut[1][$i]>$cut[2][$i]) {
				$tab1[$i]  = "#FILTRE_sup(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] . ")";
				$tab2[$i]  = $cut[3][$i];
			} else {
				$tab1[$i]  = "#FILTRE_sup(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] . ")";
				$tab2[$i]  = $cut[4][$i];
			}	
		}
			
		$res = str_replace($tab1, $tab2, $res);
			
		preg_match_all("`\#FILTRE_inf\(([^\|]*)\|\|([^\|]*)\|\|([^\|]*)\|\|([^\)]*)\)`", $res, $cut);
        $tab1 = "";
		$tab2 = "";
		
		for($i=0; $i<count($cut[2]); $i++) {
			if($cut[1][$i]<$cut[2][$i]) {
				$tab1[$i]  = "#FILTRE_inf(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] . ")";
				$tab2[$i]  = $cut[3][$i];
			} else {
				$tab1[$i]  = "#FILTRE_inf(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] . ")";
				$tab2[$i]  = $cut[4][$i];
			}	
		}
			
		$res = str_replace($tab1, $tab2, $res);
		preg_match_all("`\#FILTRE_supegal\(([^\|]*)\|\|([^\|]*)\|\|([^\|]*)\|\|([^\)]*)\)`", $res, $cut);
        $tab1 = "";
		$tab2 = "";
		
		for($i=0; $i<count($cut[2]); $i++) {
			if($cut[1][$i]>=$cut[2][$i]) {
				$tab1[$i]  = "#FILTRE_supegal(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] . ")";
				$tab2[$i]  = $cut[3][$i];
			} else {
				$tab1[$i]  = "#FILTRE_supegal(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] .")";
				$tab2[$i]  = $cut[4][$i];
			}	
		}
			
		$res = str_replace($tab1, $tab2, $res);
		preg_match_all("`\#FILTRE_infegal\(([^\|]*)\|\|([^\|]*)\|\|([^\|]*)\|\|([^\)]*)\)`", $res, $cut);
        $tab1 = "";
		$tab2 = "";
		
		for($i=0; $i<count($cut[2]); $i++) {
			if($cut[1][$i]<=$cut[2][$i]) {
				$tab1[$i]  = "#FILTRE_infegal(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] . ")";
				$tab2[$i]  = $cut[3][$i];
			} else {
				$tab1[$i]  = "#FILTRE_infegal(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] . ")";
				$tab2[$i]  = $cut[4][$i];
			}	
		}
			
		$res = str_replace($tab1, $tab2, $res);
		preg_match_all("`\#FILTRE_egall\{([^\|]*)\|\|([^\|]*)\|\|([^\}]*)\}`", $res, $cut);
		$tab1 = "";
		$tab2 = "";
			
		for($i=0; $i<count($cut[2]); $i++) {
        	if(trim($cut[1][$i]) == trim($cut[2][$i])) {
                $tab1[$i] = "#FILTRE_egall{" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "}";
                $tab2[$i] = $cut[3][$i];
        	} else {
                $tab1[$i] = "#FILTRE_egall{" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "}";
                $tab2[$i] = "";
			}
		}

		$res = str_replace($tab1, $tab2, $res);
		preg_match_all("`\#FILTRE_dif\{([^\|]*)\|\|([^\|]*)\|\|([^\}]*)\}`", $res, $cut);
		$tab1 = "";
		$tab2 = "";

		for($i=0; $i<count($cut[2]); $i++) {
        	if(trim($cut[1][$i]) != trim($cut[2][$i])) {
                $tab1[$i] = "#FILTRE_dif{" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "}";
                $tab2[$i] = $cut[3][$i];
        	} else {
                $tab1[$i] = "#FILTRE_dif{" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "}";
                $tab2[$i] = "";
			}
		}

		$res = str_replace($tab1, $tab2, $res);

	}
		
}
?>