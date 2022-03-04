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
require_once(realpath(dirname(__FILE__)) . "../../../../classes/Declidispdesc.class.php");

class Vendeclidisp extends PluginsClassiques
{
		public $id;
		public $venteprod;
		public $declidisp;
		public $table	= "ventedeclidisp";
		public $bddvars = array("id", "venteprod", "declidisp");

		function Vendeclidisp()
		{
				$this->Baseobj();
		}

		function charger($id=null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function boucle($texte, $args)
		{
				$id = lireTag($args, "id");
				$venteprod = lireTag($args, "venteprod");

				$Declidispdesc =  new Declidispdesc();

      	if($_SESSION['navig']->lang == "") $lang=1; else $lang=$_SESSION['navig']->lang;

      	if($id != "") $search .= " AND $this->table.id in ($id)";
				if($venteprod != "") $search .= " AND $this->table.venteprod = $venteprod";

      	$query_optlibre = "SELECT $this->table.id,$this->table.venteprod,$this->table.declidisp,
								$Declidispdesc->table.titre
	  					  FROM $this->table LEFT JOIN $Declidispdesc->table ON $this->table.declidisp = $Declidispdesc->table.declidisp
						   	WHERE 1 AND $Declidispdesc->table.lang=$lang $search";
      	$resul_optlibre = mysql_query($query_optlibre);

      	$nbres = mysql_numrows($resul_optlibre);
      	if(!$nbres) return "";

      	$compt=0;

      	while($row = mysql_fetch_object($resul_optlibre)) {
	      		$compt=$compt+1;

	      		$Declidispdesc->charger($row->id,$lang);
	      		if ($Declidispdesc->titre=="" && $Declidispdesc->option=="") {
	      				$Declidispdesc->charger($row->id,1);
	      		}

						$temp = str_replace("#ID", $row->id, $texte);
						$temp = str_replace("#VENTEPROD", $row->venteprod, $temp);
						$temp = str_replace("#DECLIDISP", $row->declidisp, $temp);
						$temp = str_replace("#TITRE", $row->titre, $temp);
						$res .= $temp;
				}

				return $res;
		}
}

?>
