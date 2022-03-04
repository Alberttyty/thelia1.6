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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Declinaison.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Declinaisondesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Venteoptlibre.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Optlibredesc.class.php");

class Optlibre extends PluginsClassiques
{
  	public $id;
  	public $ref;
  	public $prix;
  	public $prix2;
  	public $table = "optlibre";
  	public $bddvars = array("id", "ref", "prix", "prix2");

  	function Optlibre()
    {
  		  $this->PluginsClassiques("optlibre");
  	}

  	function init()
    {
  		  $query_optlibre = "CREATE TABLE `optlibre` (
  					`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  					`ref` TEXT NOT NULL ,
  					`prix` FLOAT NOT NULL ,
  					`prix2` FLOAT NOT NULL
  			);"
        ;
    		$resul_optlibre = mysql_query($query_optlibre, $this->link);
    		$ventedeclilibre = new Venteoptlibre();
    		$ventedeclilibre->init();
     		$optlibredesc = new Optlibredesc();
        $optlibredesc->init();
  	}

    function charger($ref = null, $var2 = null)
    {
        if ($ref != null) return $this->getVars("SELECT * FROM $this->table WHERE ref=\"$ref\"");
    }

    function charger_id($id)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
    }

    function charger_option($option,$ref)
    {
        if (!get_magic_quotes_gpc())$option=addslashes($option);
        return $this->getVars("SELECT * FROM $this->table WHERE option=\"$option\" AND ref=\"$ref\"");
    }

    function delete_ref($ref)
    {
        if (!get_magic_quotes_gpc()) $ref=addslashes($ref);

        $optlibredesc = new Optlibredesc();
  			$query = "delete from $this->table,$optlibredesc->table where $this->table.ref=\"" . $ref . "\" and $this->table.id=$optlibredesc->table.optlibre";

        if($ref != "") $resul = mysql_query($query, $this->link);
    }

    function delete_sauf_rubrique($exclusion="")
    {
      	$produit = new Produit();
      	$exclude="";
      	foreach(explode(",",$exclusion) as $k => $v){
      		if(intval($v)!=0)$exclude.=" AND $produit->table.rubrique!=".intval($v);
      	}
      	$optlibredesc = new Optlibredesc();
      	$query = "delete from $optlibredesc->table where optlibre in (select id from $this->table where ref in (select ref from $produit->table where 1".$exclude."))";
      	$resul = $this->query($query);
      	CacheBase::getCache()->reset_cache();
      	$query = "delete from $this->table where ref in (select ref from $produit->table where 1".$exclude.")";
      	$resul = $this->query($query);
      	CacheBase::getCache()->reset_cache();
    }

  	/***********************
  	 * BOUCLES D'AFFICHAGE *
  	 ***********************/
  	function boucle($texte, $args)
    {
  		  $boucle = lireTag($args, "boucle");

        switch ($boucle) {
          	case "option":
          		return $this->boucle_option($texte, $args);
          		break;

          	case "panier":
          		return $this->boucle_panier($texte, $args);
          		break;

            case "vente":
          		return $this->boucle_vente($texte, $args);
          		break;
        }
  	}

  	function boucle_option($texte, $args)
    {
    		$id = lireTag($args, "id");
    		$ref = lireTag($args, "ref");
    		$option = lireTag($args, "option");
     		$commencepar = lireTag($args, "commencepar");
     		$necommencepaspar = lireTag($args, "necommencepaspar");
  		  $pasvide = lireTag($args, "pasvide");
    		//$stockmini = lireTag($args, "stockmini");
    		$num = lireTag($args, "num");
     		$deb = lireTag($args, "deb");
  		  $article =  lireTag($args, "article");
     		$classement =  lireTag($args, "classement");

     		$optlibredesc =  new Optlibredesc();
    		$search ="";
    		$res="";
    		$order = "";
    		$limit="";

        if($_SESSION['navig']->lang == "") $lang=1; else $lang=$_SESSION['navig']->lang;

        if($deb == "") $deb = 0;
        if($num != "") $limit = "limit $deb,$num";

        if($classement == "titre") $order = "order by $optlibredesc->table.titre";
        if($classement == "prix") $order = "order by $this->table.prix";

        if($id != "") $search .= " and $this->table.id in ($id)";
        if($ref != "") $search .= " and $this->table.ref=\"$ref\"";
        if($commencepar != "") $search .= " and $optlibredesc->table.titre like \"$commencepar%\"";
        if($necommencepaspar != "") $search .= " and $optlibredesc->table.titre not like \"$necommencepaspar%\"";

      	$query_optlibre = "SELECT $this->table.id,$this->table.ref,$this->table.prix,$this->table.prix2
    					   FROM $this->table left join $optlibredesc->table on $this->table.id=$optlibredesc->table.optlibre
  						   WHERE 1 and $optlibredesc->table.lang=$lang $search $order $limit";
      	$resul_optlibre = mysql_query($query_optlibre, $this->link);

      	$nbres = mysql_numrows($resul_optlibre);
      	if(!$nbres) return "";

      	$compt=0;

      	while($row = mysql_fetch_object($resul_optlibre)) {
      			$prix = $row->prix - ($row->prix * $_SESSION['navig']->client->pourcentage / 100);
      			$prix2 = $row->prix2 - ($row->prix2 * $_SESSION['navig']->client->pourcentage / 100);
      			$prix = number_format($prix, 2, ".", "");
      			$prix2 = number_format($prix2, 2, ".", "");

        		$compt=$compt+1;
    		  	$prod = new Produit();
    		  	$prod->charger($row->ref);

            if($prod->promo == "1" && $prix!=0) $pourcentage = ceil((100 * ($prix - $prix2)/$prix));
  		  	  else $pourcentage=0;

        		$optlibredesc->charger($row->id,$lang);
        		if($optlibredesc->titre==""&&$optlibredesc->option=="") {
                $optlibredesc->charger($row->id,1);
        		}

        		if ($pasvide != "" && $pasvide == "1") {
                if($optlibredesc->titre=="") continue;
        		}

      			$temp = str_replace("#ID", $row->id, $texte);
      			$temp = str_replace("#REF", $row->ref, $temp);
      			$temp = str_replace("#STRIPTITRE", preg_replace("/^(\d*)\./ ","",$optlibredesc->titre), $temp);
      			$temp = str_replace("#TITRE", $optlibredesc->titre, $temp);
      			$temp = str_replace("#OPTION", $optlibredesc->option, $temp);
      			$temp = str_replace("#PRIX2", $prix2, $temp);
      			$temp = str_replace("#PRIX", $prix, $temp);
      			$temp = str_replace("#PROMO", $prod->promo, $temp);
      			$temp = str_replace("#ARTICLE", $row->article, $temp);
      			$temp = str_replace("#COMPT", $compt, $temp);
      			$temp = str_replace("#POURCENTAGE", $pourcentage, $temp);
      			$res .= $temp;
        }

      	return $res;
  	}

  	function boucle_panier($texte, $args)
    {
    		$article =  lireTag($args, "article");
    		if($article == "" || $_SESSION['navig']->optlibre[$article] == "")
    		return "";

    		$res="";

    		foreach ($_SESSION['navig']->optlibre[$article] as $key => $value) {
      			$temp = str_replace("#OPTLIBRE", $value, $texte);
      			$temp = str_replace("#ARTICLE", $article, $temp);
      			$res .= $temp;
    		}

       	return $res;
  	}

  	function boucle_vente($texte, $args)
    {
    		$id = lireTag($args, "id");
    		$venteprod = lireTag($args, "venteprod");

     		$Venteoptlibre =  new Venteoptlibre();
        $Optlibredesc =  new Optlibredesc();

        if($_SESSION['navig']->lang == "") $lang=1; else $lang=$_SESSION['navig']->lang;
        if($id != "") $search .= " AND $Venteoptlibre->table.id in ($id)";
        if($venteprod != "") $search .= " AND $Venteoptlibre->table.venteprod = $venteprod";

        $query_optlibre = "SELECT $Venteoptlibre->table.id,$Venteoptlibre->table.venteprod,$Venteoptlibre->table.optlibre,
  			$Optlibredesc->table.titre, $Optlibredesc->table.option
  	  					 FROM $Venteoptlibre->table LEFT JOIN $Optlibredesc->table ON $Venteoptlibre->table.optlibre = $Optlibredesc->table.optlibre
  						   WHERE 1 AND $Optlibredesc->table.lang=$lang $search";
        $resul_optlibre = mysql_query($query_optlibre);

        $nbres = mysql_numrows($resul_optlibre);
        if(!$nbres) return "";

        $compt=0;

        while( $row = mysql_fetch_object($resul_optlibre) ) {
        		$compt=$compt+1;

        		$Optlibredesc->charger($row->id,$lang);
        		if($Optlibredesc->titre=="" && $Optlibredesc->option=="") {
        			  $Optlibredesc->charger($row->id,1);
        		}

      			$temp = str_replace("#ID", $row->id, $texte);
      			$temp = str_replace("#VENTEPROD", $row->venteprod, $temp);
      			$temp = str_replace("#OPTLIBRE", $row->optlibre, $temp);
      			$temp = str_replace("#TITRE", $row->titre, $temp);
      			$temp = str_replace("#OPTION", $row->option, $temp);
      			$res .= $temp;
        }
  		  //echo('<pre>');print_r( $temp );echo('</pre>');exit();
      	return $res;
    }

    function ajouterPanier($indiceAjoute)
    {
        if(!empty($_REQUEST['optlibre'])) {
            if(is_array($_REQUEST['optlibre'])) {
                $_SESSION['navig']->optlibre[$indiceAjoute] = $_REQUEST['optlibre'];

                foreach ($_REQUEST['optlibre'] as $option) {
          					$this->charger_id($option);

                    if($this->prix!=0) $_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produit->prix += $this->prix;
          					if($this->prix2!=0) $_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produit->prix2 += $this->prix2;

                    $optlibredesc=new Optlibredesc();
                    $optlibredesc->charger($this->id);

                    if ($optlibredesc->titre!="") {
                  			if($_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produitdesc->chapo!="") $separation=" - ";
                  			else $separation="";

                  			$_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produitdesc->chapo .= $separation.$optlibredesc->titre;
                    }
                }
            }
        }
    }

  	function action()
    {
      	if(isset($_REQUEST['action'])) $action=$_REQUEST['action'];
      	else $action="";

      	if ($_REQUEST['action'] == "supprimer" && $_REQUEST['article'] != "") {
            $this->supprimerOption($_REQUEST['article']);
        }
    }

    function supprimerOption($index)
    {
        if(!empty($_SESSION['navig']->optlibre)) {
            $index=intval($index);
            unset($_SESSION['navig']->optlibre[$index]);
            //Ré-indexer suite à la suppression d'une case
            $_SESSION['navig']->optlibre = array_values($_SESSION['navig']->optlibre);
        }
    }

  	function apres()
    {
  	   if($reset) $_SESSION["navig"]->optlibre = array();
  	}

    function apresVenteprod($venteprod,$pos)
    {
      	if(empty($_SESSION['navig']->lang)) $lang=1; else $lang=$_SESSION['navig']->lang;

      	if(!empty($_SESSION['navig']->optlibre)) {
          	foreach($_SESSION['navig']->optlibre[$pos] as $option) {
            		$this->charger_id($option);
            		$optlibredesc = new Optlibredesc();
            		$optlibredesc->charger($this->id,$lang);

            		if($optlibredesc->titre!="") $venteprod->titre .= " - " . $optlibredesc->titre;
    				    else $venteprod->titre .= " - " . $optlibredesc->option;

                $venteprod->maj();

            		$venteoptlibre = new Venteoptlibre();
      			  	$venteoptlibre->venteprod = $venteprod->id;
      			  	$venteoptlibre->optlibre = 	$option;
      			  	$venteoptlibre->add();
          	}
      	}
    }

    function listerOptions($ref)
    {
      	$query = "select * from $this->table where ref=\"$ref\"";
      	$options=$this->query_liste($query,"Optlibre");
      	return $options;
    }
}
?>
