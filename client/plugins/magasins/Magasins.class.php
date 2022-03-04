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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");

class Magasins extends PluginsClassiques
{
		public $id;
		public $nom;
    public $description;
		public $adresse;
		public $code_postal;
		public $ville;
		public $pays;
    public $telephone;
    public $email;
		public $url;
    public $lat;
    public $lng;

		public $table="magasins";
		public $bddvars = array("id", "nom", "description", "adresse", "code_postal", "ville", "pays", "telephone", "email", "url", "lat", "lng");

		function Magasins()
		{
				$this->PluginsClassiques("Magasins");
		}

		function charger($id = null, $lang = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function init()
		{
			  $this->ajout_desc("Magasins", "Magasins", "", 1);

				$cnx = new Cnx();
				$query_commentaires = "CREATE TABLE `magasins` (
					  `id` int(11) NOT NULL auto_increment,
					  `nom` text NOT NULL,
					  `description` text NOT NULL,
					  `adresse` varchar(255) NOT NULL,
		        `code_postal` varchar(255) NOT NULL,
		        `ville` varchar(255) NOT NULL,
		        `pays` varchar(255) NOT NULL,
		        `telephone` varchar(255) NOT NULL,
		        `email` varchar(255) NOT NULL,
		        `url` varchar(255) NOT NULL,
		        `lat` varchar(255) NOT NULL,
		        `lng` varchar(255) NOT NULL,
					  PRIMARY KEY  (`id`)
				) AUTO_INCREMENT=1 ;"
				;
				$resul_commentaires = mysql_query($query_commentaires, $cnx->link);
		}

    function boucle($texte, $args)
		{
	      $boucle = lireTag($args, "boucle");
	      if ($boucle=="") $boucle='magasins';

	      $res="";

	      switch ($boucle) {
		        case 'magasins':
		        $res=$this->boucle_magasins($texte, $args);
		        break;

		       /*case 'pagination':
		        $res=$this->boucle_pagination($texte, $args);
		        break;*/
	      }

	      return $res;
    }
    /*
    function boucle_pagination($texte, $args)
		{
	      $id_contenu= lireTag($args, "id_contenu");
				$valide= lireTag($args, "valide");
				$classement= lireTag($args, "classement");
	      $deb= lireTag($args, "deb");
	      $num= lireTag($args, "num");
	      $page= lireTag($args, "page");

	      $pagination_suivante=0;
	      $pagination_precedente=0;

	      $search ="";
				$res="";
				$order="";
	      $limit="";

				// préparation de la requête
				if($id_contenu!="")  $search.=" and id_contenu=\"$id_contenu\"";
				if($valide!="")  $search.=" and valide=\"$valide\"";
				if($classement!="")  $order.=" order by $classement";
	      if($deb!=""&&$num!="")  {$deb=intval($deb);$num=intval($num);$limit=" limit $deb,$num";}

				$commentaire = new Commentairescontenu();

				$query_commentaires = "select * from $commentaire->table where 1 $search $order $limit;";
				$resul_commentaires = mysql_query($query_commentaires, $commentaire->link);
				$nbres = mysql_num_rows($resul_commentaires);

	      if ($page!="")  {
		        if (isset($_REQUEST['pagination'])) $pagination=intval($_REQUEST['pagination']);
		        else $pagination=1;
		        if ($pagination==0)$pagination=1;

		        $pagination_suivante_res=($pagination*$page);
		        $pagination_suivante=$pagination+1;
		  			if($nbres<$pagination_suivante_res) $pagination_suivante=0;

		        $pagination_precedente=$pagination-1;
		  			if($pagination_precedente<1) $pagination_precedente=0;
	      }

	      $res=$texte;

	      $res = str_replace("#PAGINATION_SUIVANTE",$pagination_suivante,$res);
	      $res = str_replace("#PAGINATION_PRECEDENTE",$pagination_precedente,$res);

				return $res;
		}*/

		function boucle_magasins($texte, $args)
		{
				// récupération des arguments
				$id_magasin= lireTag($args, "id_magasin");
	      $pays= lireTag($args, "pays");
				$classement= lireTag($args, "classement");
	      $deb= lireTag($args, "deb");
	      $num= lireTag($args, "num");
	      /*$page= lireTag($args, "page");*/

				if ($deb == '') $deb = 0;

				$search ="";
				$res="";
				$order="";
	      $limit="";

				// préparation de la requête
				if ($id_magasin!="")  $search.=" and id_magasin=\"$id_magasin\"";
	      if ($pays!="")  $search.=" and pays=\"$pays\"";
				if ($classement!="")  $order.=" order by $classement";
	      if ($num != "") {
						$deb=intval($deb);
						$num=intval($num);
						$limit=" limit $deb,$num";
				}
	      /*
				if($page!="")  {
		        if (isset($_REQUEST['pagination'])) $pagination=intval($_REQUEST['pagination']);
		        else $pagination=1;
		        if ($pagination==0)$pagination=1;
		        $deb=($pagination*$page)-$page;
		        $limit=" limit $deb,$page";
	      }
	      */

				$magasins = new Magasins();

				$query_magasins = "select * from $magasins->table where 1 $search $order $limit;";
				$resul_magasins = mysql_query($query_magasins, $magasins->link);
				$nbres = mysql_num_rows($resul_magasins);
				if(!$nbres) return "";

	      $compteur=0;

				while($row = mysql_fetch_object($resul_magasins)) {
		        $compteur=$compteur+1;
						$temp = str_replace("#NOM", $row->nom, $texte);
						$temp = str_replace("#DESCRIPTION", $row->description, $temp);
						$temp = str_replace("#ADRESSE", $row->adresse, $temp);
						$temp = str_replace("#CODE_POSTAL", $row->code_postal, $temp);
						$temp = str_replace("#VILLE",$row->ville,$temp);
						$temp = str_replace("#PAYS",$row->pays,$temp);
		        $temp = str_replace("#TELEPHONE",$row->telephone,$temp);
		        $temp = str_replace("#EMAIL",$row->email,$temp);
		        $temp = str_replace("#URL",$row->url,$temp);
		        $temp = str_replace("#LAT",$row->lat,$temp);
		        $temp = str_replace("#LNG",$row->lng,$temp);
		        $temp = str_replace("#COMPT",$compteur,$temp);
		        $temp = str_replace("#NBRES_TOTAL", $nbres, $temp);

						$res .= $temp;
				}

	      /*
	      $pagination_suivante=$pagination+1;
	      $pagination_suivante_res=($pagination_suivante*$page);
				if($nbres<$pagination_suivante_res) $pagination_suivante=0;

	      $pagination_precedente=$pagination-1;
				if($pagination_precedente<1) $pagination_precedente=0;

	      $res = str_replace("#PAGINATION_SUIVANTE",$pagination_suivante,$res);
	      $res = str_replace("#PAGINATION_PRECEDENTE",$pagination_precedente,$res);
	      */

				return $res;
		}
}
?>
