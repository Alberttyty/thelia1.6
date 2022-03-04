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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdisp.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdispdesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracteristique.class.php");

class Bouclecaracdispdesc extends PluginsClassiques
{
		function Bouclecaracdispdesc()
		{
				$this->PluginsClassiques("Bouclecaracdispdesc");
		}

		function init()
		{
		  	$this->ajout_desc("Bouclecaracdispdesc", "Bouclecaracdispdesc", "", 1);
	  }

		function boucle($texte, $args)
		{
				// récupération des arguments
				$titre= lireTag($args, "titre");
				$lang= lireTag($args, "lang");
				$caracteristique= lireTag($args, "caracteristique");

				$search = '';
				$res = '';
				$order = '';

				$caracdisp = new Caracdisp();
				$caracdispdesc = new Caracdispdesc();

		      	if($lang != '') $lang=1;

				// préparation de la requête
				if ($titre != '')  $search .= ' AND '.$caracdispdesc->table.'.titre = "'.$titre.'"';
				if ($lang != '')  $search .= ' AND '.$caracdispdesc->table.'.lang = "'.$lang.'"';
				if ($caracteristique != '') $search .= ' AND '.$caracdisp->table.'.caracteristique = "'.$caracteristique.'"';

				$query = 'SELECT '.$caracdispdesc->table.'.caracdisp
						  FROM '.$caracdispdesc->table.','.$caracdisp->table.'
						  WHERE 1 AND '.$caracdisp->table.'.id = '.$caracdispdesc->table.'.caracdisp '.$search.' '.$order;
				$resul = mysql_query($query, $caracdispdesc->link);
				$nbres = mysql_numrows($resul);

				if(!$nbres) return '';

				while( $row = mysql_fetch_object($resul)) {
				  	$temp = str_replace('#CARACDISP', $row->caracdisp, $texte);
						$res .= $temp;
				}

				return $res;
		}

		function action() {}

		function post() {}
}

?>
