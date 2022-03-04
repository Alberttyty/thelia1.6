<?php
/*************************************************************************************/
/*                                                                                   */
/*      Plugin Recherche         			                                  		 				 */
/*                                                                                   */
/*      Copyright (c) 2010, Franck Allimant		                                     	 */
/*			email : thelia@allimant.org           	                             	     	 */
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
require_once(realpath(dirname(__FILE__)) . '/stemmer/StemmingToolKit.php');

class Recherche extends PluginsClassiques {

		const VERSION = '1.2.3';

		function Recherche() {
				$this->PluginsClassiques("recherche");
		}

		function init() {
				$this->ajout_desc(
                "Recherche améliorée",
                "Plugin de recherche amélioré",
                "Ce plugin améliore la recherchede Thélia, en normalisant les mots recherchés, anfin de remonter des résultats plus pertinents.",
                1);
		}

    function action() {
	    	global $res;

	    	$mode == 'et';
	    	$exact = 0;

	    	if(isset($_REQUEST['recherche_mode'])) {
	    		$mode = strtolower($_REQUEST['recherche_mode']);
	    		if($mode == 'et' || $mode == 'ou') $res = str_replace('#RECHERCHE_MODE', $mode, $res);
	    	}

	    	if(isset($_REQUEST['recherche_exacte'])) $exact = intval($_REQUEST['recherche_exacte']) > 0 ? 1 : 0;

	    	if(strstr($res, '#RECHERCHE_') !== false) {
	    			$res = str_replace('#RECHERCHE_MODE', $mode, $res);
	    			$res = str_replace('#RECHERCHE_EXACTE', $exact, $res);
	    	}
    }

		function boucle($texte, $args) {
        $boucle = lireTag($args, 'boucle');

        if ($boucle == '') $boucle="produit";

        switch(strtolower($boucle))
        {
            case 'produit':
                return $this->boucleProduit($texte, $args);
            break;

            case 'rubrique':
                return $this->boucleRubrique($texte, $args);
            break;

            case 'contenu':
                return $this->boucleContenu($texte, $args);
            break;

            case 'page':
                return $this->bouclePage($texte, $args);
            break;
        }
    }

    function chercher($query, $boucle, $texte, $args, $nombre = false, $pour_comptage = false)
    {
    	$motcle = trim(lireTag($args, "motcle"));
    	$mode   = trim(lireTag($args, "mode"));
    	$exact   = intval(trim(lireTag($args, "exact")));
    	$nombre = lireTag($args, "num");

    	$nombre = $nombre == '' ? false : intval($nombre);

    	if ($mode == '') $mode = 'et';
    	if ($motcle == '') return '';

    	$res = '';

    	$tk = new StemmingToolkit();

			$words = $tk->indexText($motcle, 'fr', $exact ? false : true);

			$where = array();

			foreach($words['index'] as $index) {
					$word = $index['stem'];

					if ($exact) $exp = "REGEXP '[[:<:]]${word}[[:>:]]'";
					else $exp = "LIKE '%$word%'";

					if($boucle == 'boucleProduit') $where[] = "(ref $exp OR pd.titre $exp OR pd.chapo $exp  OR pd.description $exp  OR pd.postscriptum $exp OR rd.titre $exp)";
					else $where[] = "(titre $exp OR chapo $exp  OR description $exp  OR postscriptum $exp)";
			}

			if (count($where) > 0) {
					$query = "$query WHERE ".implode($mode == 'et' ? ' AND ' : ' OR ', $where);

		    	if($nombre) {
							$page = $this->get_current_page();

							if ($page <= 0) $page = 1;

							$offset = $nombre * ($page - 1);
							$query .= " LIMIT $offset, $nombre";

							$this->rechparams = '';
		    	}

					$ids = array();

					$result = mysql_query($query);

					if ($result) {
							if ($pour_comptage) return mysql_num_rows($result);

							while ($row = mysql_fetch_object($result)) {
									if (intval($row->id != 0)) $ids[] = $row->id;
							}
					}

					if (count($ids) > 0) {
							$pargs = preg_replace('/motcle="([^"]+)"/i', 'id="'.implode(',', $ids).'"', $args);
							$res = $boucle($texte, $pargs);
					}
			}

			return $res;
    }

		function boucleProduit($texte, $args, $nombre = false, $pour_comptage = false) {
				return $this->chercher(
									"SELECT distinct p.id
									 FROM produitdesc pd
									 LEFT JOIN produit p ON p.id=pd.produit
									 LEFT JOIN rubriquedesc rd ON rd.id=p.rubrique",
									 'boucleProduit',
									$texte,
									$args,
									$nombre,
									$pour_comptage
				);
		}

		function boucleRubrique($texte, $args, $nombre = false, $pour_comptage = false) {
				return $this->chercher(
									"select distinct r.id
									 from rubriquedesc rd
									 LEFT JOIN rubrique r ON r.id=rd.rubrique",
									 'boucleRubrique',
									$texte,
									$args,
									$nombre,
									$pour_comptage
				);
		}

		function boucleContenu($texte, $args, $nombre = false, $pour_comptage = false) {
				return $this->chercher(
									"select distinct c.id
									 from contenudesc cd
									 LEFT JOIN contenu c ON c.id=cd.contenu",
									 'boucleContenu',
									$texte,
									$args,
									$nombre,
									$pour_comptage
				);
		}

    // Ceci est un quasi copier-coller de la boucle 'page' de Thélia,
    // vu qu'on ne peut pas l'utiliser pour faire notre pagination
    // on est obligés de la recopier. C'est nul :-(
    private function bouclePage($texte, $args) {
				$num = lireTag($args, "num");
				$courante = lireTag($args, "courante");
				$pagecourante = lireTag($args, "pagecourante");
				$typeaff = lireTag($args, "typeaff");
				$max = lireTag($args, "max");
				$affmin = lireTag($args, "affmin");
        $avance = lireTag($args, "avance");
        $type_page = lireTag($args, "type_page");
				$motcle = urlencode(lireTag($args, "motcle"));

				$i=0;

				$args = str_replace('num=', '__num__=', $args);

				switch(strtolower($type_page)) {
           case 'produit':
                $nbres = $this->boucleProduit($texte, $args, false, true);
            break;

            case 'rubrique':
                $nbres = $this->boucleRubrique($texte, $args, false, true);
            break;

            case 'contenu':
                $nbres = $this->boucleContenu($texte, $args, false, true);
            break;

            default:
            	return '';
				}

        $page = $this->get_current_page();

				if($page<=0) $page=1;
				$bpage=$page;

				$res="";

				$page = $bpage;

				$nbpage = ceil($nbres/$num);
				if($page+1>$nbpage) $pagesuiv=$page;
				else $pagesuiv=$page+1;

				if($page-1<=0) $pageprec=1;
				else $pageprec=$page-1;

				if($nbpage<$affmin) return;
				if($nbpage == 1) return;

				if($typeaff == 1) {
						if(!$max) $max=$nbpage+1;
						if($page && $max && $page>$max) $i=ceil(($page)/$max)*$max-$max+1;

						if($i == 0) $i=1;

						$fin = $i+$max;

						for (; $i<$nbpage+1 && $i<$fin; $i++ ) {
								$temp = str_replace("#PAGE_NUM", "$i", $texte);
								$temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
								$temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
								$temp = str_replace("#MOTCLE", $motcle, $temp);

								if($pagecourante && $pagecourante == $i) {
										if($courante =="1" && $page == $i ) $res .= $temp;
										else if($courante == "0" && $page != $i ) $res .= $temp;
										else if($courante == "") $res .= $temp;
								}

								else if(!$pagecourante) $res .= $temp;
						}

				} else if($typeaff == "0" && ($avance == "precedente" && $pageprec != $page)) {
                $temp = str_replace("#PAGE_NUM", "$page", $texte);
                $temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
								$temp = str_replace("#MOTCLE", $motcle, $temp);
                $res .= $temp;

        } else if($typeaff == "0" && ($avance == "suivante" && $pagesuiv != $page)) {
                $temp = str_replace("#PAGE_NUM", "$page", $texte);
                $temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
								$temp = str_replace("#MOTCLE", $motcle, $temp);
                $res .= $temp;

        } else if($typeaff == "0" && $avance == "") {
                $temp = str_replace("#PAGE_NUM", "$page", $texte);
                $temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
                $temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
								$temp = str_replace("#MOTCLE", $motcle, $temp);
                $res .= $temp;
        }

				return $res;
    }

    private function get_current_page() {
    		return isset($_REQUEST['rechpage']) ? intval($_REQUEST['rechpage']) : 0;
    }
}
?>
