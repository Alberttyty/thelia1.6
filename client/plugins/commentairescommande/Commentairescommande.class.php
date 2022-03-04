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

class Commentairescommande extends PluginsClassiques
{
		public $id;
		public $id_commande;
		public $texte;

		public $table="commentairescommande";
		public $bddvars = array("id", "id_commande", "texte");

		function Commentairescommande()
		{
				$this->PluginsClassiques("Commentairescommande");
		}

		function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

    function charger_commande($id_commande)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE id_commande=\"$id_commande\"");
		}

		function init()
		{
			  $this->ajout_desc("Commentairescommande", "Commentairescommande", "", 1);

				$cnx = new Cnx();
				$query_commentaires = "CREATE TABLE `commentairescommande` (
					  `id` int(11) NOT NULL auto_increment,
					  `id_commande` int(11) NOT NULL,
					  `texte` text NOT NULL,
					  PRIMARY KEY  (`id`)
				) AUTO_INCREMENT=1 ;"
				;
				$resul_commentaires = mysql_query($query_commentaires, $cnx->link);
		}

    function boucle($texte, $args)
		{
	      $id_commande=lireTag($args,"commande");

	      $search ="";
				$res="";
				$order="";
	      $limit="";

	      if ($id_commande!="")  $search.=" and id_commande=\"$id_commande\"";

	      $commentaire = new Commentairescommande();

				$query_commentaires = "select * from $commentaire->table where 1 $search $order $limit;";
				$resul_commentaires = mysql_query($query_commentaires, $commentaire->link);

				$nbres = mysql_numrows($resul_commentaires);
	      if (!$nbres) return "";

	      while($row = mysql_fetch_object($resul_commentaires)) {
		        $temp = str_replace("#TEXTE",nl2br($row->texte),$texte);
		        $res .= $temp;
	      }

				return $res;
    }
}
?>
