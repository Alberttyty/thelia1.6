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
?>
<?php                        
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Promoutil.class.php");
	
	class Bouclepromoutil extends PluginsClassiques{

		function Bouclepromoutil(){
	
			$this->PluginsClassiques("Bouclepromoutil");	
	
		}
    
    function init(){
		  $this->ajout_desc("Bouclepromoutil", "Bouclepromoutil", "", 1);
		}
    
    function boucle($texte, $args) {
    
			$commande = lireTag($args,"commande");

			$search ="";

			$res="";

			// préparation de la requête
			if ($commande!="")  $search.=" and commande=\"$commande\"";

			$promoutil = new Promoutil();	
			$query_promoutil = "select * from $promoutil->table where 1 $search";

			$resul_promoutil = $this->query($query_promoutil);

			if ($resul_promoutil) {

				$nbres = $this->num_rows($resul_promoutil);

				if ($nbres > 0) {
                
					while($row = $this->fetch_object($resul_promoutil)){
            
						$temp = $texte;
            
            $temp = str_replace("#COMMANDE", $row->commande, $temp);
            $temp = str_replace("#ID", $row->id, $temp);
						$temp = str_replace("#PROMO", $row->promo, $temp);
						$temp = str_replace("#CODE", $row->code, $temp);
						$temp = str_replace("#TYPE", $row->type, $temp);
						$temp = str_replace("#VALEUR", $row->valeur, $temp);

						$res .= $temp;
					}
				}

			}

			return $res;
	
		}
		
	}


?>
