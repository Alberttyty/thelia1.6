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
include_once(realpath(dirname(__FILE__)) . "/Agendadesc.class.php");

class Agenda extends PluginsClassiques
{
		public $id;
		public $lieu;
		public $lien;
		public $datedebut;
		public $datefin;
		public $classement;

		public $table="agenda";
		public $bddvars = ["id","lieu","lien","datedebut","datefin","classement"];

		function Agenda()
		{
				$this->PluginsClassiques("Agenda");
		}

		function charger($id = null, $lang = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function init()
		{
		  	$this->ajout_desc("Agenda", "Agenda", "", 1);
				$cnx = new Cnx();

				$query = "CREATE TABLE `agenda` (
					  `id` int(11) NOT NULL auto_increment,
					  `lieu` text NOT NULL,
					  `lien` text NOT NULL,
					  `datedebut` date NOT NULL,
					  `datefin` date NOT NULL,
					  `classement` int(11) NOT NULL,
					  PRIMARY KEY  (`id`)
				) AUTO_INCREMENT=1 ;"
				;
				$resul = mysql_query($query, $cnx->link);
		      	$query = "CREATE TABLE `agendadesc` (
					  `id` int(11) NOT NULL auto_increment,
					  `agenda` int(11) NOT NULL,
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
				$datesfutures= lireTag($args, "datesfutures");
				$datespassees= lireTag($args, "datespassees");
	      $nombre= lireTag($args, "nombre");

				$search ="";
				$res="";
				$order="";
	      $limite="";

				// préparation de la requête
				if ($id!="")  $search.=" and id=\"$id\"";
				if ($datesfutures=="1")  $search.=" and datedebut>=NOW()";
				if ($datespassees=="1")  $search.=" and datefin>=NOW()";
				if ($classement=="manuel")  $order.=" order by classement";
				if ($classement=="datedebut")  $order.=" order by datedebut";
				if ($classement=="inverse")  $order.=" order by datedebut desc";
				if($nombre!="")  $limite.=" limit 0,".$nombre;

				$agenda = new Agenda();

				/*
				if (ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE) {
					// On retourne uniquement les rubriques traduites
					$search .= " and $agendadesc->table.id is not null";
				}
				*/
				$query_agenda = "select * from $agenda->table where 1 $search $order $limite";
				$resul_agenda = mysql_query($query_agenda, $agenda->link);
				$nbres = mysql_num_rows($resul_agenda);

				if (!$nbres) return "";

				//setlocale (LC_TIME, 'fr_FR.utf8','fra');

	      $compteur=0;

				while($row = mysql_fetch_object($resul_agenda)) {
	          $agendadesc = new Agendadesc();
	        	$agendadesc->charger($row->id);

	        	$compteur++;

						$temp = str_replace("#ID", "$row->id", $texte);
	        	$temp = str_replace("#COMPT", $compteur, $temp);
						$temp = str_replace("#TITRE", "$agendadesc->titre", $temp);
						$temp = str_replace("#LIEU", "$row->lieu", $temp);
						$temp = str_replace("#DESCRIPTION", "$agendadesc->description", $temp);
						$temp = str_replace("#LIEN", "$row->lien", $temp);
	        	$temp = str_replace("#DATEDEBUTISO8601", date("c",strtotime(substr($row->datedebut, 0, 10))), $temp);
	        	$temp = str_replace("#DATEFINISO8601", date("c",strtotime(substr($row->datefin, 0, 10))), $temp);
						$temp = str_replace("#DATEDEBUTLONGUE", $this->dateEnFrancais(date("l j F Y",strtotime(substr($row->datedebut, 0, 10)))), $temp);
						$temp = str_replace("#DATEFINLONGUE", $this->dateEnFrancais(date("l j F Y",strtotime(substr($row->datefin, 0, 10)))), $temp);
						$temp = str_replace("#DATEDEBUTCOURTE", $this->dateEnFrancais(date("j F",strtotime(substr($row->datedebut, 0, 10)))), $temp);
						$temp = str_replace("#DATEFINCOURTE", $this->dateEnFrancais(date("j F",strtotime(substr($row->datefin, 0, 10)))), $temp);
						$temp = str_replace("#DATEDEBUTJOUR", date("j",strtotime(substr($row->datedebut, 0, 10))), $temp);
	        	$temp = str_replace("#DATEDEBUTMOIS", $GLOBALS['dicotpl']['mois_'.date("m",strtotime(substr($row->datedebut, 0, 10)))], $temp);
						$temp = str_replace("#DATEDEBUTANNEE", date("Y",strtotime(substr($row->datedebut, 0, 10))), $temp);
						$temp = str_replace("#DATEFINJOUR", date("j",strtotime(substr($row->datefin, 0, 10))), $temp);
						$temp = str_replace("#DATEFINMOIS", $GLOBALS['dicotpl']['mois_'.date("m",strtotime(substr($row->datefin, 0, 10)))], $temp);
						$temp = str_replace("#DATEFINANNEE", date("Y",strtotime(substr($row->datefin, 0, 10))), $temp);
						$temp = str_replace("#DATEFIN", date("d/m/y",strtotime(substr($row->datefin, 0, 10))), $temp);
						$temp = str_replace("#DATEDEBUT", date("d/m/y",strtotime(substr($row->datedebut, 0, 10))), $temp);

						$res .= $temp;
				}

				return $res;
		}

    function dateEnFrancais($date)
		{
				$moisen = [
						"",
						"January",
						"February",
						"March",
						"April",
						"May",
						"June",
						"July",
						"August",
						"September",
						"October",
						"November",
						"December",
				];

				$jouren = [
						"Monday",
						"Tuesday",
						"Wednesday",
						"Thursday",
						"Friday",
						"Saturday",
						"Sunday",
				];

				$moisfr = [
						"",
						"Janvier",
						"Février",
						"Mars",
						"Avril",
						"Mai",
						"Juin",
						"Juillet",
						"Août",
						"Septembre",
						"Octobre",
						"Novembre",
						"Décembre",
				];

				$jourfr = [
						"Lundi",
						"Mardi",
						"Mercredi",
						"Jeudi",
						"Vendredi",
						"Samedi",
						"Dimanche",
				];


				$date=str_ireplace($moisen,$moisfr,$date);
				$date=str_ireplace($jouren,$jourfr,$date);

				return $date;
    }

		function action()
		{
				global $res;
	  		$res = str_replace("#AGENDA_ID", $_REQUEST['id_agenda'], $res);
	  		return $res;
		}

		function post() {}

    function changer_classement($id, $type)
		{
				$this->charger($id);
				$remplace = new Agenda();

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
