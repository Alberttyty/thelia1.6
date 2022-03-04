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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubrique.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Image.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
include_once(realpath(dirname(__FILE__)) . "/classes/Googleshoppingimage.class.php");

class Googleshopping extends PluginsClassiques
{
		public $id;
		public $rubrique;
		public $googleproductcategory;

		public $table="googleshopping";
		public $bddvars = array("id", "rubrique", "googleproductcategory");

		function __construct()
		{
				parent::__construct("googleshopping");
		}

		function init()
		{
				$this->ajout_desc("Google Shopping", "Google Shopping", "", 1);
				$cnx = new Cnx();
				$query_googleshopping = "CREATE TABLE `googleshopping` (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					  `rubrique` INT NOT NULL ,
					  `googleproductcategory` TEXT NOT NULL
					  )"
				;
				$resul_googleshopping = mysql_query($query_googleshopping, $cnx->link);

	      $googleshoppingimage = new Googleshoppingimage();
				$googleshoppingimage->init();
		}

		function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function charger_rubrique($rubrique)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE rubrique=\"$rubrique\"");
		}

    function modrub($rubrique)
		{
	      if(isset($_REQUEST['googleproductcategory'])) {
		        if($_REQUEST['googleproductcategory']!='') {
			          $googleproductcategory = $_REQUEST['googleproductcategory'];
			          $googleproductcategory = str_replace(">", "&gt;", $googleproductcategory);

			          $this->rubrique='';
			          $this->charger_rubrique($rubrique->id);
			          $this->googleproductcategory=$googleproductcategory;

			          if ($this->rubrique!='') $this->maj();
			          else {
				            $this->rubrique=$rubrique->id;
				            $this->add();
			          }
		        }
		        else {
			          $this->charger_rubrique($rubrique->id);
			          $this->delete();
		        }
	      }
    }

    function modfichier($objdesc)
		{
	      if(isset($_REQUEST['couleurs_photo'])) {
		        $googleshoppingimage = new Googleshoppingimage();

		        if ($_REQUEST['couleurs_photo']!='') {
			          $googleshoppingimage->charger_image($obj->id);
			          $googleshoppingimage->couleurs = $_REQUEST['couleurs_photo'];
			          $googleshoppingimage->couleurs = str_replace("/", "&#47;", $googleshoppingimage->couleurs);

			          if($googleshoppingimage->image!='') $googleshoppingimage->maj();
			          else {
				            $googleshoppingimage->image = $_REQUEST['id_photo'];
				            $googleshoppingimage->add();
			          }
		        }
		        else {
			          $googleshoppingimage->charger_image($_REQUEST['id_photo']);
			          $googleshoppingimage->delete();
		        }
	      }
    }

    function suprub($rubrique)
		{
	      $this->rubrique='';
	      $this->charger_rubrique($rubrique->id);

	      if ($this->rubrique!='') $this->delete();
    }

    function charger_recursif($rubrique)
		{
	      $googleshopping = new Googleshopping();

	      while ($googleshopping->rubrique==''){
		        $marubrique = new Rubrique();
			      $marubrique->charger($rubrique);
		        $googleshopping->charger_rubrique($marubrique->id);
		        $rubrique=$marubrique->parent;
		        if($rubrique==0) break;
	      }

	      if($googleshopping->id!=0) $this->charger($googleshopping->id);
    }

    function boucle($texte, $args)
		{
	      $boucle = lireTag($args, "boucle");

	      switch ($boucle) {
		        case "googleproductcategory":
		        return $this->boucle_googleproductcategory($texte, $args);
		        break;

		        case "googleshoppingimage":
		        return $this->boucle_googleshoppingimage($texte, $args);
		        break;

		        case "couleur":
		        return $this->boucle_couleur($texte, $args);
		        break;
	      }
    }

    function boucle_couleur($texte, $args)
		{
      	$googleshoppingimage = new Googleshoppingimage();
      	$image = new Image();

      	$res="";
      	$search="";
      	$order="";

      	$id = lireTag($args, "image");
      	if($id!='')$search .= "$googleshoppingimage->table.image='".$id."'";

      	$query = "select * from $googleshoppingimage->table where 1 and $search $order";
				$resul = $this->query($query);

      	if ($resul) {
						$nbres = $this->num_rows($resul);

						if ($nbres > 0) {
	          		while( $row = $this->fetch_object($resul)){
	              		$temp = $texte;
	              		$temp = str_replace("#ID", $row->id, $temp);
	    							$temp = str_replace("#IMAGE", $row->image, $temp);
	              		$temp = str_replace("#COULEURSCLASS", str_replace("-","",ereg_caracspec(html_entity_decode($row->couleurs))), $temp);
	              		$temp = str_replace("#COULEURS",$row->couleurs, $temp);

										$res .= $temp;
	          		}
						}
      	}

      	return $res;
    }

    function boucle_googleshoppingimage($texte, $args)
		{
	      $googleshoppingimage = new Googleshoppingimage();
	      $image = new Image();
	      $produit = new Produit();

	      $res="";
	      $search="";
	      $order="";

	      $produits=array();

	      $order = lireTag($args, "classement");
	      if($order=="classement"||$order=="")$order .= "order by $image->table.classement";

	      $ligne = lireTag($args, "ligne");
	      if($ligne=='1')$search .= " and $produit->table.ligne='1'";
	      $ref = lireTag($args, "ref");
	      if($ref!='')$search .= " and $produit->table.ref='".$ref."'";

	      $query = "select $image->table.produit as produit,$googleshoppingimage->table.image as image,$googleshoppingimage->table.id as id,$googleshoppingimage->table.couleurs as couleurs,$produit->table.ref as ref from $googleshoppingimage->table,$image->table,$produit->table where 1 and $googleshoppingimage->table.image=$image->table.id and $image->table.produit=$produit->table.id $search $order";

				$resul = $this->query($query);

	      if ($resul) {
						$nbres = $this->num_rows($resul);

						if ($nbres > 0) {
			          while( $row = $this->fetch_object($resul)){
				            if(!in_array($row->ref,$produits)) {
					              $temp = $texte;
					              $temp = str_replace("#ID", $row->id, $temp);
					    					$temp = str_replace("#IMAGE", $row->image, $temp);
					              $temp = str_replace("#COULEURS",$row->couleurs, $temp);
					              $temp = str_replace("#PRODUIT",$row->produit, $temp);
					              $temp = str_replace("#REF",$row->ref, $temp);
					  						$res .= $temp;
					              array_push($produits,$row->ref);
				            }
			          }
		        }
	      }

      	return $res;
    }

    function boucle_googleproductcategory($texte, $args)
		{
				// récupération des arguments
				$rubrique = lireTag($args, "rubrique");

				$res="";
	      $search="";

				// préparation de la requête
				if ($rubrique!="")  {
	        $this->charger_recursif($rubrique);
	        $search.=" and id=\"$this->id\"";
	      }

				$query = "select * from $this->table where 1 $search";

				$resul = $this->query($query);

				if ($resul) {
						$nbres = $this->num_rows($resul);

						if ($nbres > 0) {
								while($row = $this->fetch_object($resul)) {
			  						$temp = $texte;
			  						$temp = str_replace("#ID", $row->id, $texte);
			  						$temp = str_replace("#RUBRIQUE", $row->rubrique, $temp);
			              $temp = str_replace("#GOOGLEPRODUCTCATEGORY",$row->googleproductcategory, $temp);
									  $res .= $temp;
								}
						}
				}

				return $res;
		}

    function post()
		{
      	global $res;

      	preg_match_all("`\#FILTRE_googleshopping\(\|([^\|]*)([^\)]*)\|\)`", $res, $cut);
		    $tab1 = "";
		    $tab2 = "";

		    for($i=0; $i<count($cut[1]); $i++) {
            $modif = html_entity_decode($cut[1][$i],ENT_QUOTES,'UTF-8');
            $modif = preg_replace("/(\r\n|\n|\r)/", " ", $modif);
            $u = $GLOBALS['meta']['pcre_u'];
            $modif = preg_replace('/\s+/S'.$u, " ", $modif);
            $modif = preg_replace("/(&nbsp;| )+/S", " ", $modif);
            $modif = trim($modif);
			      $tab1[$i] = "#FILTRE_googleshopping(|" . $cut[1][$i] . "|)";
		        $tab2[$i] = $modif;
		    }

		    $res = str_replace($tab1, $tab2, $res);
    }
}
?>
