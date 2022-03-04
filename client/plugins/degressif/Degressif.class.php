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

class Degressif extends PluginsClassiques
{
		public $id;
		public $ref;
		public $tranchemin;
		public $tranchemax;
		public $prix;
		public $prix2;

		public $table = "degressif";

		public $bddvars=array("id", "ref", "tranchemin", "tranchemax", "prix", "prix2");

		function Degressif()
		{
				$this->PluginsClassiques("degressif");
		}

		function init()
		{
				$query_degressif = "CREATE TABLE `degressif` (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`ref` TEXT NOT NULL ,
						`tranchemin` FLOAT NOT NULL ,
						`tranchemax` FLOAT NOT NULL ,
						`prix` FLOAT NOT NULL ,
						`prix2` FLOAT NOT NULL
				);"
				;
				$resul_degressif = mysql_query($query_degressif, $this->link);
		}

    function charger($ref = null, $var2 = null)
		{
        if ($ref != null) return $this->getVars("SELECT * FROM $this->table WHERE ref=\"$ref\"");
    }

    function charger_id($id)
		{
        return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
    }

     function charger_tranche($ref, $quantite)
		 {
        return $this->getVars("SELECT * FROM $this->table WHERE ref=\"$ref\" AND $quantite>=tranchemin AND $quantite<tranchemax");
    }

		function boucle($texte, $args)
		{
				$id = lireTag($args, "id");
				$ref = lireTag($args, "ref");
				$tranchemix = lireTag($args, "tranchemix");
				$tranchemax = lireTag($args, "tranchemax");

	      $search ="";
	      $res="";
				$order = "order by tranchemin";
	      $limit="";

	      if($num != "") $limit = "limit 0,$num";

		    if($id != "") $search .= " and id=\"$id\"";
	      if($ref != "") $search .= " and ref=\"$ref\"";
	      if($tranchemin != "") $search .= " and tranchemin >=\"$tranchemin\"";
	      if($tranchemax != "") $search .= " and tranchemax >=\"$tranchemax\"";

	      $query_degressif = "select * from $this->table where 1 $search $order $limit";
	      $resul_degressif = mysql_query($query_degressif, $this->link);

	      $nbres = mysql_numrows($resul_degressif);
	      if(!$nbres) return "";

	      while( $row = mysql_fetch_object($resul_degressif)){
		        $temp = str_replace("#ID", $row->id, $texte);
		        $temp = str_replace("#REF", $row->ref, $temp);
		        $temp = str_replace("#TRANCHEMIN", $row->tranchemin, $temp);
		        $temp = str_replace("#TRANCHEMAX", $row->tranchemax, $temp);
		        $temp = str_replace("#PRIX2", $row->prix2, $temp);
		        $temp = str_replace("#PRIX", $row->prix, $temp);
		        $res .= $temp;
	      }

			  return $res;
		}

    function ajouterPanier($indiceAjoute)
		{
	      $degressif = new Degressif();

				if($degressif->charger_tranche($_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produit->ref, $_SESSION['navig']->panier->tabarticle[$indiceAjoute]->quantite)){
					  $_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produit->prix = $degressif->prix;
						$_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produit->prix2 = $degressif->prix2;
				}
    }

    function action()
		{
				global $res,$action;

				if ($action == "modifier") {
						$degressif = new Degressif();
						if($degressif->charger_tranche($_SESSION['navig']->panier->tabarticle[$_REQUEST['article']]->produit->ref, $_SESSION['navig']->panier->tabarticle[$_REQUEST['article']]->quantite)){
								$_SESSION['navig']->panier->tabarticle[$_REQUEST['article']]->produit->prix = $degressif->prix;
								$_SESSION['navig']->panier->tabarticle[$_REQUEST['article']]->produit->prix2 = $degressif->prix2;
						}
				}
		}

    function modprod($produit)
		{
	      $degressif = new Degressif();
	      $query = "delete from $degressif->table where ref=\"".$produit->ref."\"";
	      $resul = $degressif->query($query);

	      for ($i=0;$i<100;$i++) {
		        if (isset($_REQUEST['degressif_tranchemin_'.$i])) {
			          $degressif = new Degressif();
			          $degressif->ref=$produit->ref;
			          $degressif->tranchemin=$_REQUEST['degressif_tranchemin_'.$i];
			          $degressif->tranchemax=$_REQUEST['degressif_tranchemax_'.$i];
			          $degressif->prix=$_REQUEST['degressif_prix_'.$i];
			          $degressif->prix2=$_REQUEST['degressif_prix2_'.$i];
			          $degressif->add();
		        }
	      }
    }
}

?>
