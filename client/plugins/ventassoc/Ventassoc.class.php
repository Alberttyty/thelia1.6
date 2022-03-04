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
include_once(realpath(dirname(__FILE__)) . "../../../../classes/Venteprod.class.php");
include_once(realpath(dirname(__FILE__)) . "../../../../classes/Commande.class.php");

class VentAssoc extends PluginsClassiques
{
		public $id;
		public $date_exec;
		public $nb_ventes;
		public $table		= "ventassoc";
		public $bddvars	= array("id", "date_exec", "nb_ventes");

		function VentAssoc()
		{
				$this->Baseobj();
		}

		function charger($id=null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function init()
		{
				$cnx = new Cnx();
				$query_ventassoc = "CREATE TABLE `ventassoc` (
				  `id` int(11) NOT NULL,
				  `date_exec` datetime NOT NULL,
				   `nb_ventes` int(11) NOT NULL
				);"
				;
				$resul_ventassoc = mysql_query($query_ventassoc, $cnx->link);
				$query_ventassoc = "INSERT INTO `ventassoc` (`id`,`date_exec`,`nb_ventes`) VALUES (1, '0000-00-00 00:00:00', 0);";
				$resul_ventassoc = mysql_query($query_ventassoc, $cnx->link);
		}

		function destroy()
		{
				$cache = new Cache();
	    	$cache->vider("VENTASSOC", "%");
		}

		function boucle($texte, $args)
		{
				// récupération des arguments
				$ref		= lireTag($args, "ref");
				$num		= lireTag($args, "num");
				$classement	= lireTag($args, "classement");

				if ($num != "" && $classement!="vente")	$limit = "limit 0,$num";

				$search ="";
				$res="";

				switch ($classement) {
						case 'prixmin'	: $ordre = "ORDER BY prixu ASC";
						break;

						case 'prixmax'	: $ordre = "ORDER BY prixu DESC";
						break;

						case 'aleatoire': $ordre = "ORDER BY RAND()";
						break;
				}

				$venteprod = new Venteprod();
				$query_ventassoc = "select distinct(commande) from $venteprod->table where 1 and ref=\"$ref\"";
				$resul_ventassoc = mysql_query($query_ventassoc, $venteprod->link);

				$row1 = mysql_num_rows($resul_ventassoc);
				if (!$row1) return "";

				$liste_commandes = "";

				while ($row1 = mysql_fetch_object($resul_ventassoc)) $liste_commandes .= $row1->commande . ', ';

				$liste_commandes = substr($liste_commandes, 0, strlen($liste_commandes) - 2);

				$query_ventassoc2 = "select DISTINCT(ref), titre, prixu from $venteprod->table where commande in($liste_commandes) and ref<>\"$ref\" $ordre $limit";
				$resul_ventassoc2 = mysql_query($query_ventassoc2, $venteprod->link);

	  		//Si classé en fonction du nombre de vente
	  		if($classement=="vente") {
		   			$ventes=array();
		    		while($row2 = mysql_fetch_object($resul_ventassoc2)) {
		      			if(array_key_exists($row2->ref,$ventes)) $ventes[$row2->ref]=$ventes[$row2->ref]+1;
		      			else $ventes[$row2->ref]=1;
						}

						asort($ventes);
		    		if($num=="") $num=100;

						$compteur=0;
			    	foreach($ventes as $key => $value) {
			      		$temp = str_replace("#REF",$key,$texte);
			  				$res .= $temp;
			      		$compteur=$compteur+1;
			      		if($compteur>=$num) break;
			    	}
		  	}
		  	//sinon
		  	else {
			  		while($row2 = mysql_fetch_object($resul_ventassoc2)) {
				  			$temp	  = str_replace("#REF", $row2->ref, $texte);
				  			$res	 .= $temp;
			  		}
		  	}

				$ventassoc = new VentAssoc();
				$ventassoc->charger();
				$ventassoc->date_exec = date("Y-m-d H:i:s");

				$venteprod = new Venteprod();
				$query_ventassoc = "select count(*) as nb from $venteprod->table";
				$resul_ventassoc = mysql_query($query_ventassoc, $venteprod->link);

				$ventassoc->nb_ventes = mysql_result($resul_ventassoc, 0, "nb");
				$ventassoc->maj();

				return $res;
		}

		function action()
		{
			 	$ventassoc = new VentAssoc();
			 	$ventassoc->charger();

				$venteprod = new Venteprod();
			 	$query_ventassoc = "select count(*) as nb from $venteprod->table";
			 	$resul_ventassoc = mysql_query($query_ventassoc, $venteprod->link);
			 	$nb_ventes = mysql_result($resul_ventassoc, 0, "nb");

			 	$commande = new Commande();
			 	$query_ventassoc = "SELECT * FROM `commande` order by date desc limit 0,1";
				$resul_ventassoc = mysql_query($query_ventassoc, $commande->link);
			 	$row = mysql_fetch_object($resul_ventassoc);

			 	if($nb_ventes != $ventassoc->nb_ventes || $row->date>$ventassoc->date_exec) {
	     			$cache = new Cache();
	        	$cache->vider("VENTASSOC", "%");
		 		}
		}
}

?>
