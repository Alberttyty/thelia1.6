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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Panier.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Client.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");

class Prixachat extends PluginsClassiques
{
    public $id;
		public $produit;
		public $prixachat;

    public $table="prixachat";
		public $bddvars = array("id", "produit", "prixachat");

		function __construct()
		{
				parent::__construct("prixachat");
		}

		function init()
		{
				$this->ajout_desc("Prixachat", "Prixachat", "", 1);
				$cnx = new Cnx();
				$query_prixachat = "CREATE TABLE `prixachat` (
			      `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			      `produit` INT NOT NULL ,
			      `prixachat` FLOAT NOT NULL DEFAULT '0'
			      )"
				;
				$resul_prixachat = mysql_query($query_prixachat, $cnx->link);
		}

		function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function charger_produit($produit)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE produit=\"$produit\"");
		}

    function modprod($produit)
		{
	      if(isset($_REQUEST['prixachat'])) {
		        $prixachat=$_REQUEST['prixachat'];
		        $prixachat = str_replace(",", ".", $prixachat);

		        $this->produit='';
		        $this->charger_produit($produit->id);
		        $this->prixachat=$prixachat;

		        if($this->produit!='') $this->maj();
		        else {
			          $this->produit=$produit->id;
			          $this->add();
		        }
	      }
    }
}

?>
