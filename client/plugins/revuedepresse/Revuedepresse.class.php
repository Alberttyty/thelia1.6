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
include_once(realpath(dirname(__FILE__)) . "/Revuedepressedesc.class.php");

class Revuedepresse extends PluginsClassiques
{
		public $id;
    public $source;
		public $lien;
		public $fichier;
		public $date;
		public $classement;

		public $table="revuedepresse";
		public $bddvars = ["id","source","lien","fichier","date","classement"];

		function Revuedepresse()
		{
				$this->PluginsClassiques("Revuedepresse");
		}

		function charger($id = null, $lang = null)
		{
				if ($id != null) return $this->getVars("SELECT $this->table.id,source,lien,fichier,date,classement FROM $this->table WHERE id=\"$id\"");
		}

		function init()
		{
			  $this->ajout_desc("Revue de presse", "Revue de presse", "", 1);
				$cnx = new Cnx();

				$query = "CREATE TABLE `revuedepresse` (
					  `id` int(11) NOT NULL auto_increment,
					  `source` text NOT NULL,
					  `lien` text NOT NULL,
					  `fichier` varchar(250) NOT NULL,
					  `date` date NOT NULL,
					  `classement` int(11) NOT NULL,
					  PRIMARY KEY  (`id`)
				) AUTO_INCREMENT=1 ;"
				;
				$resul = mysql_query($query, $cnx->link);
	      $query = "CREATE TABLE `revuedepressedesc` (
					  `id` int(11) NOT NULL auto_increment,
					  `revuedepresse` int(11) NOT NULL,
					  `titre` text NOT NULL,
					  `description` text NOT NULL,
					  `lang` 	int(11)	 NOT NULL,
					  PRIMARY KEY  (`id`)
				) AUTO_INCREMENT=1 ;"
				;

				$resul = mysql_query($query,$cnx->link);
		}

		function boucle($texte, $args)
		{
				// récupération des arguments
				$id= lireTag($args, "id");
				$classement= lireTag($args, "classement");
	      $nombre= lireTag($args, "nombre");

				$search ="";
				$res="";
				$order="";
	      $limite="";

				// préparation de la requête
				if ($id!="")  $search.=" and id=\"$id\"";
				if ($classement=="manuel")  $order.=" order by classement";
				if ($classement=="date")  $order.=" order by date";
				if ($classement=="inverse")  $order.=" order by date desc";
	      if ($nombre!="")  $limite.=" limit 0,".$nombre;

				$revuedepresse = new Revuedepresse();

				$query_revuedepresse = "select * from $revuedepresse->table where 1 $search $order $limite";
				$resul_revuedepresse = mysql_query($query_revuedepresse, $revuedepresse->link);
				$nbres = mysql_num_rows($resul_revuedepresse);

				if (!$nbres) return "";

				while($row = mysql_fetch_object($resul_revuedepresse)) {

		        $revuedepressedesc = new Revuedepressedesc();
		        $revuedepressedesc->charger($row->id);

		        if ($row->fichier!="") $fichier=FICHIER_URL."client/document/".$row->fichier;
		        else $fichier="";

		        if ($row->date!=""&&$row->date!="0000-00-00") {
			          $date_iso8601=date("c",strtotime(substr($row->date, 0, 10)));
			          $date_jour=date("j",strtotime(substr($row->date, 0, 10)));
			          $date_mois=$GLOBALS['dicotpl']['mois_'.date("m",strtotime(substr($row->date, 0, 10)))];
			          $date_annee=date("Y",strtotime(substr($row->date, 0, 10)));
			          $date=date("d/m/y",strtotime(substr($row->date, 0, 10)));
		        }
		        else {
			          $date_iso8601="";
			          $date_jour="";
			          $date_mois="";
			          $date_annee="";
			          $date="";
		        }

					  $temp = str_replace("#ID", "$row->id", $texte);
						$temp = str_replace("#TITRE", "$revuedepressedesc->titre", $temp);
						$temp = str_replace("#SOURCE", "$row->source", $temp);
						$temp = str_replace("#DESCRIPTION", "$revuedepressedesc->description", $temp);
						$temp = str_replace("#LIEN", "$row->lien", $temp);
		        $temp = str_replace("#FICHIER", $fichier, $temp);
		        $temp = str_replace("#DATEISO8601",$date_iso8601, $temp);
						$temp = str_replace("#DATEJOUR",$datejour, $temp);
		        $temp = str_replace("#DATEMOIS",$date_mois, $temp);
		        $temp = str_replace("#DATEANNEE",$date_annee, $temp);
						$temp = str_replace("#DATE",$date, $temp);

						$res .= $temp;
				}

				return $res;
		}

    function changer_classement($id, $type)
		{
				$this->charger($id);
				$remplace = new Revuedepresse();

				if ($type == "M") $res = $remplace->getVars("SELECT * FROM $this->table WHERE classement<" . $this->classement . " ORDER BY classement DESC LIMIT 0,1");
				else if ($type == "D") $res = $remplace->getVars("SELECT * FROM $this->table WHERE classement>" . $this->classement . " ORDER BY classement LIMIT 0,1");

				if (! $res) return "";

				$sauv = $remplace->classement;

				$remplace->classement = $this->classement;
				$this->classement = $sauv;

				$remplace->maj();
				$this->maj();
		}
}
?>
