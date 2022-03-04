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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Modules.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Ventedeclibre.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Declibreintitule.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Declibreintituledesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Declibredesc.class.php");

class Declibre extends PluginsClassiques
{
  	public $id;
  	public $ref;
  	public $stock;
  	public $prix;
  	public $prix2;
    public $lien;

  	public $table = "declibre";

  	public $bddvars=array("id", "ref", "stock", "prix", "prix2", "lien");


  	function Declibre()
    {
  		  $this->PluginsClassiques("declibre");
  	}

  	function init()
    {
  		  $query_declibre = "CREATE TABLE IF NOT EXISTS `declibre` (
  					`id` BIGINT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  					`ref` TEXT NOT NULL ,
  					`stock` FLOAT NOT NULL ,
  					`prix` FLOAT NOT NULL ,
  					`prix2` FLOAT NOT NULL,
            `lien` VARCHAR(255) NOT NULL
  			);"
        ;
  		  $resul_declibre = mysql_query($query_declibre, $this->link);

    		$ventedeclilibre = new Ventedeclibre();
    		$ventedeclilibre->init();

    		$declilibreintitule = new Declibreintitule();
    		$declilibreintitule->init();

        $declilibreintituledesc = new Declibreintituledesc();
  		  $declilibreintituledesc->init();

        $declilibredesc = new Declibredesc();
  		  $declilibredesc->init();

        /* MAJ ancien */
        $query_declibre = "SHOW COLUMNS FROM `declibre` LIKE 'declinaison'";
        $resul_declibre = mysql_query($query_declibre, $this->link);

        if(mysql_num_rows($resul_declibre)!=0) {
          	$query_declibre = "SELECT id,declinaison FROM `declibre` WHERE 1";
          	$resul_declibre = mysql_query($query_declibre, $this->link);

          	while ($declibre = mysql_fetch_object($resul_declibre)) {
            		$declilibredesc = new Declibredesc();

            		if (!$declilibredesc->charger($declibre->id)) {
                 		$declilibredesc->declibre=$declibre->id;
                 		$declilibredesc->declinaison=$declibre->declinaison;
                 		$declilibredesc->lang=1;
                 		$declilibredesc->add();
            		}
          	}
            /*$query_declibre = "SELECT id,titre1,description1,titre2,description2 FROM `declibreintitule` WHERE 1";
            $resul_declibre = mysql_query($query_declibre, $this->link);
            while($declilibreintitule = mysql_fetch_object($resul_declibre)){
              $declilibreintituledesc = new Declibreintituledesc();
              if(!$declilibreintituledesc->charger($declilibreintitule->id)){
                 $declilibreintituledesc->declilibreintitule=$declilibreintitule->id;
                 $declilibreintituledesc->titre1=$declilibreintitule->titre1;
                 $declilibreintituledesc->description1=$declilibreintitule->description2;
                 $declilibreintituledesc->titre2=$declilibreintitule->titre1;
                 $declilibreintituledesc->description2=$declilibreintitule->description2;
                 $declilibreintituledesc->lang=1;
                 $declilibreintituledesc->add();
              }
            }*/
            //$query_declibre = "ALTER TABLE `declibre` DROP `declinaison`";
            //$resul_declibre = mysql_query($query_declibre, $this->link);
            //$query_declibre = "ALTER TABLE `declibre` CHANGE `decli_ref` `lien` VARCHAR(255)";
            //$resul_declibre = mysql_query($query_declibre, $this->link);
        }
  	}

    function charger($ref = null, $var2 = null)
    {
        if ($ref != null) return $this->getVars("SELECT * FROM $this->table WHERE ref=\"$ref\"");
    }

    function charger_id($id)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
    }

    function charger_lien($lien)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE lien=\"$lien\"");
    }

    function charger_declinaison($declinaison, $ref)
    {
        /*if (!get_magic_quotes_gpc())*/ $declinaison=addslashes($declinaison);

        $declilibredesc = new Declibredesc();
        $query = "select declibre from $declilibredesc->table where declinaison=\"$declinaison\" and ref=\"$ref\"";
  		  $resul_declibredesc = mysql_query($query, $this->link);
        $row_declibredesc = mysql_fetch_object($resul_declibredesc);

        return $this->getVars("select * from $this->table where id=\"$row_declibredesc->declibre\"");
    }

    function delete_ref($ref)
    {
      	if ($ref != "") {
      			$declilibredesc = new Declibredesc();
      			$query = "delete from $declibredesc->table where declibre in (select id from $this->table where ref=\"" . $ref . "\")";
      			$resul = $this->query($query);
      			CacheBase::getCache()->reset_cache();
      			$query = "delete from $this->table where $this->table.ref=\"" . $ref . "\"";
      			$resul = $this->query($query);
      			CacheBase::getCache()->reset_cache();
      	}
    }

    function delete_sauf_rubrique($exclusion="")
    {
      	$produit = new Produit();
      	$exclude="";

        foreach(explode(",",$exclusion) as $k => $v) {
        	 if (intval($v)!=0) $exclude.=" AND $produit->table.rubrique!=".intval($v);
      	}

        $declibredesc = new Declibredesc();
      	$query = "delete from $declibredesc->table where declibre in (select id from $this->table where ref in (select ref from $produit->table where 1".$exclude."))";
      	$resul = $this->query($query);
      	CacheBase::getCache()->reset_cache();
      	$query = "delete from $this->table where ref in (select ref from $produit->table where 1".$exclude.")";
      	$resul = $this->query($query);
      	CacheBase::getCache()->reset_cache();
    }

    function supprod($produit)
    {
      	$this->delete_ref($produit->ref);
    }

    function demarrage()
    {
      	global $action,$nouveau;
      	if ($_REQUEST['action'] == "ajouter" && $_REQUEST['id_declibre'] != "" && $_REQUEST['ref'] != "") {
        	 if($this->testNouveauDansPanier($_REQUEST['ref'],$_REQUEST['id_declibre'])) $nouveau=1;
      	}
    }

    function testNouveauDansPanier($ref,$id_declibre)
    {
      	$trouve = 1;
      	//si id_declibre ne correspond pas au produit, on annule tout
      	$declibre = new Declibre();
      	$declibre->charger_id($id_declibre);

      	if ($declibre->ref != $ref) {
          	$action = "";
          	$_REQUEST['action'] = "";
          	$trouve=0;
      	}

      	for($i = 0; $i<$_SESSION['navig']->panier->nbart; $i++) {
          	if($ref == $_SESSION['navig']->panier->tabarticle[$i]->produit->ref && $_SESSION['navig']->declibre[$i] == $id_declibre) {
            	 $trouve = 0;
          	}
      	}

      	if($trouve == 1) return true;
        return false;
    }

  	function boucle($texte, $args)
    {
  		  $boucle = lireTag($args, "boucle");

        switch ($boucle) {
        		case "declinaison":
          			return $this->boucle_declinaison($texte, $args);
          			break;
    		  	/*
            case "valeur":
      		  		return $this->boucle_valeur($texte, $args);
      		  		break;
            */
    	    	case "intitule":
          			return $this->boucle_intitule($texte, $args);
          			break;

        		case "panier":
          			return $this->boucle_panier($texte, $args);
          			break;
        }
  	}

  	function boucle_intitule($texte, $args)
    {
     		$id = lireTag($args, "id");
    		$ref = lireTag($args, "ref");

        $search ="";
        $res="";
      	$order = "order by id";
        $limit="";

        if ($id != "") $search .= " and id=\"$id\"";
        if ($ref != "") $search .= " and ref=\"$ref\"";

    	  $declilibreintitule = new Declibreintitule();

        $query_declibre = "select * from $declilibreintitule->table where 1 $search $order $limit";
        $resul_declibre = mysql_query($query_declibre, $this->link);

        $nbres = mysql_numrows($resul_declibre);
        if (!$nbres) return "";

        while($row = mysql_fetch_object($resul_declibre)) {
      			if ($_SESSION['navig']->lang == "") $lang=1; else $lang=$_SESSION['navig']->lang;

            $intituledesc=new Declibreintituledesc();
      			$intituledesc->charger($row->id,$lang);

            if ($intituledesc->declibreintitule=="") $intituledesc->charger($row->id,1);

            $temp = str_replace("#ID", $row->id, $texte);
          	$temp = str_replace("#REF", $row->ref, $temp);
          	$temp = str_replace("#TITRE1", $intituledesc->titre1, $temp);
          	$temp = str_replace("#DESCRIPTION1", $intituledesc->description1, $temp);
          	$temp = str_replace("#TITRE2", $intituledesc->titre2, $temp);
          	$temp = str_replace("#DESCRIPTION2", $intituledesc->description2, $temp);

            $res .= $temp;
      	}

        return $res;
    }

  	function boucle_declinaison($texte, $args)
    {
    		$id = lireTag($args, "id");
    		$ref = lireTag($args, "ref");
    		$declinaison = lireTag($args, "declinaison");
    		$pasvide = lireTag($args, "pasvide");
    		$stockmini = lireTag($args, "stockmini");
    		$num = lireTag($args, "num");
    		$article =  lireTag($args, "article");
        $classement =  lireTag($args, "classement");

        $search ="";
        $res="";
  		  $order = "";
        $limit="";

        if ($num != "") $limit = "limit 0,$num";

  	    if ($id != "") $search .= " and $this->table.id=\"$id\"";
        if ($ref != "") $search .= " and $this->table.ref=\"$ref\"";
        /*if ($declinaison != "") $search .= " and declinaison=\"$declinaison\"";
        if ($pasvide != "" && $pasvide == "1") $search .= " and declinaison!=\"\"";*/
        if ($stockmini != "") $search .= " and $this->table.stock>=\"$stockmini\"";
        if ($_SESSION['navig']->lang == "") $lang=1; else $lang=$_SESSION['navig']->lang;

        $declibredesc=new Declibredesc();

      	$query_declibre = "select $this->table.id,$this->table.ref,$declibredesc->table.declinaison,$this->table.stock,$this->table.prix,$this->table.prix2 from $this->table left join $declibredesc->table on ($declibredesc->table.declibre=$this->table.id) where 1 and $declibredesc->table.lang=$lang $search $order $limit";
      	$resul_declibre = mysql_query($query_declibre, $this->link);

      	$nbres = mysql_numrows($resul_declibre);
      	if (!$nbres) return "";

        $compt=0;

        $rows=array();

        while($row = mysql_fetch_object($resul_declibre)) {
      			if ($row->declinaison=="") {
      			  $declibredesc->charger($row->id,1);
      			  $row->declinaison=$declibredesc->declinaison;
      			}
      			if ($pasvide != "" && $pasvide == "1") {
      			    if ($row->declinaison=="") continue;
      			}

      			array_push($rows,$row);
        }

        if ($classement == "taille_vetement") usort($rows,array("Declibre","parTailleVetement"));

        foreach($rows as $key=>$row) {
      			$prix = $row->prix - ($row->prix * $_SESSION['navig']->client->pourcentage / 100);
      			$prix2 = $row->prix2 - ($row->prix2 * $_SESSION['navig']->client->pourcentage / 100);
      			$prix = number_format($prix, 2, ".", "");
      			$prix2 = number_format($prix2, 2, ".", "");

      			$prod = new Produit();
      			$prod->charger($row->ref);
      			if ($prod->promo == "1" && $prix!=0) $pourcentage = ceil((100 * ($prix - $prix2)/$prix));
      			else $pourcentage=0;

      			$compt=$compt+1;

      			$temp = str_replace("#ID", $row->id, $texte);
      			$temp = str_replace("#REF", $row->ref, $temp);
      			$temp = str_replace("#DECLINAISON", $row->declinaison, $temp);
      			$temp = str_replace("#STOCK", $row->stock, $temp);
      			$temp = str_replace("#PRIX2", $prix2, $temp);
      			$temp = str_replace("#PRIX", $prix, $temp);
      			$temp = str_replace("#PROMO", $prod->promo, $temp);
      			$temp = str_replace("#ARTICLE", $row->article, $temp);
      			$temp = str_replace("#COMPT", $compt, $temp);
      			$temp = str_replace("#POURCENTAGE", $pourcentage, $temp);

      			$res .= $temp;
  	    }

        if ($compt==0) return "";

  	    return $res;
  	}

    function parTailleVetement($a,$b)
    {
      	$valeur_a="";
      	$valeur_b="";

      	if (preg_match('/(^|\s)(xxs|xs|s|m|l|xl|xxl|xxxl|xxxxl){1}\b/',strtolower($a->declinaison),$retour))
          	$valeur_a=$retour[2];

      	if (preg_match('/(^|\s)(xxs|xs|s|m|l|xl|xxl|xxxl|xxxxl){1}\b/',strtolower($b->declinaison),$retour))
          	$valeur_b=$retour[2];

      	if ($valeur_a!=""&&$valeur_b!="") {
      			$sizes = [
      				"xxs" => 0,
      				"xs" => 1,
      				"s" => 2,
      				"m" => 3,
      				"l" => 4,
      				"xl" => 5,
      				"xxl" => 6,
      				"xxxl" => 7,
      				"3xl" => 7,
      				"xxxxl" => 8,
      				"4xl" => 8
      			];

          	$asize = $sizes[$valeur_a];
          	$bsize = $sizes[$valeur_b];

            if ($asize == $bsize) return 0;

            return ($asize > $bsize) ? 1 : -1;
        }
        else return 0;
    }

  	function boucle_panier($texte, $args)
    {
    		$article =  lireTag($args, "article");

    		if ($article == "" || $_SESSION['navig']->declibre[$article] == "") return "";

    		$this->charger_id($_SESSION['navig']->declibre[$article]);

    		$texte = str_replace("#DECLIBRE", $_SESSION['navig']->declibre[$article], $texte);
    		$texte = str_replace("#STOCK", $this->stock, $texte);
    		$texte = str_replace("#ARTICLE", $article, $texte);

    		return $texte;
  	}

    function ajouterPanier($indiceAjoute)
    {
    		if($_REQUEST['id_declibre'] != "" && $indiceAjoute !== false) {
    		 	  $this->ajouterDeclibrePanier($_REQUEST['id_declibre'],$indiceAjoute);
    		}
    }

  	function action()
    {
    		global $res;

    		$res = str_replace("#DECLIBRE_ID", $_REQUEST['id_declibre'], $res);

    		if ($_REQUEST['action'] == "supprimer" && $_REQUEST['article'] != "") {
      			for($i=$_REQUEST['article']; $i <= $_SESSION['navig']->panier->nbart; $i++) {
        				if(isset($_SESSION['navig']->declibre[$i+1])) {
                    $_SESSION['navig']->declibre[$i] = $_SESSION['navig']->declibre[$i+1];
        				}
                else unset($_SESSION['navig']->declibre[$i]);
      			}
    		}
  	}

    function ajouterDeclibrePanier($id_declibre,$num_article)
    {
      	global $nouveau;

      	$declibre=new Declibre();

      	if ($declibre->charger_id($id_declibre)) {
      			$_SESSION['navig']->declibre[$num_article] = $declibre->id;
      			if ($declibre->prix!=0) $_SESSION['navig']->panier->tabarticle[$num_article]->produit->prix = $declibre->prix;
      			if ($declibre->prix2!=0) $_SESSION['navig']->panier->tabarticle[$num_article]->produit->prix2 = $declibre->prix2;
      			/*else if($declibre->prix!=0) $_SESSION['navig']->panier->tabarticle[$num_article]->produit->prix2 = $declibre->prix;*/
      	}
    }

  	function apres()
    {
    		if ($reset) $_SESSION["navig"]->declibre = array();
  	}

  	function aprescommande($commande)
    {
        for ($i=0; $i< $_SESSION['navig']->panier->nbart; $i++) {
            if ($_SESSION['navig']->declibre[$i]) {
                $declibre = new Declibre();
                $declibre->charger_id($_SESSION['navig']->declibre[$i]);

        				try {
            				$modules = new Modules();
            				if ($modules->charger_id($commande->paiement)) {
            						$modpaiement = ActionsModules::instance()->instancier($modules->nom);
            						$defalqcmd = $modpaiement->defalqcmd;
            				}
              	}
                catch (Exception $ex) {
              		  $defalqcmd = "";
              	}

                if ($defalqcmd) {
                    $declibre->stock -= $_SESSION['navig']->panier->tabarticle[$i]->quantite;
        					  $declibre->maj();
                    //mail('thierry@pixel-plurimedia.fr', 'Test Apres Commande', 'defalqcmd:'.$defalqcmd.' declibre->id:'.$declibre->id.' quantite:'.$_SESSION['navig']->panier->tabarticle[$i]->quantite.' declibre->stock(apres):'.$declibre->stock);
                }

        				$venteprod = new Venteprod();
        				$query = "select * from $venteprod->table where commande=\"" .$commande->id . "\" order by id limit $i,1";
        				$resul = mysql_query($query, $venteprod->link);
        				$row = mysql_fetch_object($resul);

                if ($_SESSION['navig']->lang == "") $lang=1; else $lang=$_SESSION['navig']->lang;
                $declibredesc = new Declibredesc();
                $declibredesc->charger($declibre->id,$lang);

        				if ($declibredesc->declinaison=="") $declibredesc->charger($declibre->id,1);

        				$tmp = new Venteprod();
        				$tmp->charger($row->id);

        				if ($declibredesc->declinaison!="") $tmp->titre .= $declibredesc->declinaison;
        				$tmp->maj();

        				$ventedeclibre = new Ventedeclibre();
        				$ventedeclibre->venteprod = $row->id;
        				$ventedeclibre->declibre = 	$_SESSION['navig']->declibre[$i];
        				$ventedeclibre->add();

        				//$_SESSION['navig']->declibre[$i]="";
        		}
        }

        $this->statut($commande,1);
  	}

  	function modprod($produit)
    {
      	$lang=$_POST['lang'];

      	$query_declibre = "select * from $this->table where ref=\"". $produit->ref . "\"";
      	$resul_declibre = mysql_query($query_declibre, $this->link);

      	while ($row_declibre = mysql_fetch_object($resul_declibre)) {
      		  $this->charger_id($row_declibre->id);

          	$declibredesc = new Declibredesc();
          	$declibredesc->charger($row_declibre->id, $lang);
          	$declibredesc->lang=$lang;

          	if ($_POST['declibretitre_'.$row_declibre->id]!="")
                $declibredesc->declinaison = $_POST['declibretitre_'.$row_declibre->id];

          	if ($_POST['declibrestock_'.$row_declibre->id]!="")
      			    $this->stock = $_POST['declibrestock_'.$row_declibre->id];

          	if ($_POST['declibreprix_'.$row_declibre->id]!="")
      			    $this->prix = $_POST['declibreprix_'.$row_declibre->id];

      		  if($_POST['declibreprix2_'.$row_declibre->id]!="")
          		  $this->prix2 = $_POST['declibreprix2_'.$row_declibre->id];

          	//if($_POST['declibrelien_'.$row_declibre->id]!="")
          		  $this->lien = $_POST['declibrelien_'.$row_declibre->id];

      			$espaces=array("  ", "   ", "    ", "     ", "      ");
      			$declibredesc->declinaison=str_replace($espaces, " ", $declibredesc->declinaison);

    		  	$this->maj();

          	if ($declibredesc->declibre=="") {
            		$declibredesc->declibre=$row_declibre->id;
            		$declibredesc->add();
          	}
            else $declibredesc->maj();
    		}

      	$query = "select stock from $this->table where ref=\"" . $produit->ref . "\"";
      	$resul = mysql_query($query, $this->link);

      	// changer le stock du produit si on a au moins une déclinaison
      	if (mysql_num_rows($resul)!=0) {
            $this->maj_stock($produit);
      			//ne pas mettre a jour les prix
      			//$this->maj_prix($produit);
      	}

      	$intitule=new Declibreintitule();
      	$intitule->charger_ref($produit->ref);

      	if ($intitule->id!="") $intitule->maj();
    	  else {
    		    $intitule->ref=$produit->ref;
      		  $intitule->id=$intitule->add();
      	}

      	$intituledesc=new Declibreintituledesc();
    	  $intituledesc->charger($intitule->id,$lang);
      	$intituledesc->lang=$lang;
      	$intituledesc->titre1 = $_POST['declibreintitule1'];
    	  $intituledesc->titre2 = $_POST['declibreintitule2'];

		    if ($intituledesc->declibreintitule=="") {
          	$intituledesc->declibreintitule=$intitule->id;
          	$intituledesc->add();
      	}
        else $intituledesc->maj();
    }

    function maj_stock($produit)
    {
      	$query = "select sum(stock) as total from $this->table where ref=\"" . $produit->ref . "\"";
    	  $resul = mysql_query($query, $this->link);
      	$produit->stock = mysql_result($resul,0, "total");
  	  	$produit->maj();
    }

    function maj_prix($produit)
    {
      	$query = "select max(prix) as lepluscher from $this->table where ref=\"" . $produit->ref . "\"";
    	  $resul = mysql_query($query, $this->link);
      	$prix=mysql_result($resul,0, "lepluscher");

      	if ($prix!=0) {
      			$produit->prix = $prix;
      			$produit->maj();
      	}

  	  	$query = "select max(prix2) as lepluscher from $this->table where ref=\"" . $produit->ref . "\"";
      	$resul = mysql_query($query, $this->link);
      	$prix2=mysql_result($resul,0, "lepluscher");

      	if ($prix2!=0) {
          	$produit->prix2 = $prix2;
          	$produit->maj();
      	}
    }

    function statut($commande,$ancienStatut="")
    {
      	error_log("Declibre : commande->paiement : ".$commande->paiement);
      	error_log("Declibre : ancienStatut : ".$ancienStatut);

      	if ($ancienStatut=="") $ancienStatut=1;

      	error_log("Declibre : ancienStatut : ".$ancienStatut);

      	try {
          	$modules = new Modules();

          	if($modules->charger_id($commande->paiement)) {
            		//error_log("modules->nom:".$modules->nom);
            		$modpaiement = ActionsModules::instance()->instancier($modules->nom);
            		//error_log("modpaiement->defalqcmd:".$modpaiement->defalqcmd);
            		$defalqcmd = $modpaiement->defalqcmd;
          	}
      	}
        catch (Exception $ex) {
            $defalqcmd = "";
      	}

      	if (($defalqcmd&&$commande->statut==1) || ((!$defalqcmd && ($ancienStatut==1 || $ancienStatut==5)) && $commande->statut==2)) {
      			$venteprod = new Venteprod();
      			$query = "select * from $venteprod->table where commande='" . $commande->id . "'";
      			$resul = mysql_query($query, $venteprod->link);

      			//error_log("QUERY:".$query);

          	while($row = mysql_fetch_object($resul)) {
            		$vdec = new Ventedeclibre();

            		$query2 = "select * from $vdec->table where venteprod='" . $row->id . "'";
            		$resul2 = mysql_query($query2, $vdec->link);

            		$produit=new Produit();
            		$produit->charger($row->ref);

            		error_log("Declibre : produit->stock : ".$produit->stock);

            		//error_log("QUERY2:".$query2);
            		//error_log("defalqcmd:".$defalqcmd);
            		//error_log("commande->facture:".$commande->facture);

                while ($row2 = mysql_fetch_object($resul2)) {
                		$declibre = new Declibre();
                		//error_log("row2->declibre:".$row2->declibre);
                		$declibre->charger_id($row2->declibre);
                		error_log("Declibre : declibre->stock :".$declibre->stock.'-'.$row->quantite);
                		$declibre->stock = $declibre->stock - $row->quantite;
                		$declibre->maj();
                		error_log("Declibre : declibre->stock :".$declibre->stock);
                		$declibre->maj_stock($produit);
            		}

            		error_log("Declibre : produit->stock : ".$produit->stock);
          	}
      	}

      	if (($defalqcmd || (!$defalqcmd && ($ancienStatut==2 || $ancienStatut==3 || $ancienStatut==4))) && ($commande->statut==5 || $commande->statut==1)) {
      			$venteprod = new Venteprod();
      			$query = "select * from $venteprod->table where commande='" . $commande->id . "'";
      			$resul = mysql_query($query, $venteprod->link);

      			while($row = mysql_fetch_object($resul)) {
      			  	$vdec = new Ventedeclibre();

      			  	$query2 = "select * from $vdec->table where venteprod='" . $row->id . "'";
      			  	$resul2 = mysql_query($query2, $vdec->link);

      			  	$produit=new Produit();
      			  	$produit->charger($row->ref);

      			  	while($row2 = mysql_fetch_object($resul2)) {
          					$declibre = new Declibre();
          					$declibre->charger_id($row2->declibre);
          					$declibre->stock = $declibre->stock + $row->quantite;
          					$declibre->maj();
          					$declibre->maj_stock($produit);
      			  	}
      			}

      			$commande->facture=0;
      			$commande->maj();
      	}
  	}
}
?>
