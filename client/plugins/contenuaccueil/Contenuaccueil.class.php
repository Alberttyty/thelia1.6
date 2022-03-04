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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Contenu.class.php");

class Contenuaccueil extends PluginsClassiques {
  
    public $id;
	public $contenu;
    public $visible;
    public $datepublication;
    public $datedebut;
	public $datefin;
    
    const TABLE = 'contenuaccueil';
    
    public $table = self::TABLE;
	public $bddvars = array("id", "contenu", "visible", "datepublication", "datedebut", "datefin");

	function __construct() {
		parent::__construct("contenuaccueil");
	}
    
    function charger_id($id) {
		return $this->getVars('SELECT * FROM '.$this->table.' WHERE id='.$id);
	}
    
    function charger_contenu($contenu_id) {
		return $this->getVars('SELECT * FROM '.$this->table.' WHERE contenu = "'.$contenu_id.'"');
	}

	function init() {
      
      	$query_contenuaccueil = "CREATE TABLE `".self::TABLE."` (
			  	`id` int(11) NOT NULL auto_increment,
        		`contenu` int(11) NOT NULL,
        		`visible` varchar(5) NOT NULL,
			  	`datepublication` datetime NOT NULL,
        		`datedebut` datetime NOT NULL,
        		`datefin` datetime NOT NULL,
			  	PRIMARY KEY  (`id`)
				) AUTO_INCREMENT=1 ;";

		$resul_contenuaccueil = $this->query($query_contenuaccueil);	
    
	}
    
    function modcont($contenu) {
    
      	$contenuaccueil = new Contenuaccueil();
      
      	if($contenuaccueil->charger_contenu($contenu->id)) $nouveau=false;
      	else $nouveau=true;
      
      	foreach($contenuaccueil->bddvars as $keyvar => $valuevar) {
      		$_REQUEST[$valuevar]=_sanitize_param($_REQUEST[$valuevar]);
       	}
      
      	if(isset($_REQUEST['visible'])) $contenuaccueil->visible =  $_REQUEST['visible'];
      	else $contenuaccueil->visible = "non";
      
      	if($_REQUEST['datedebut']!="") {
			$madate=explode("/",$_REQUEST['datedebut']);
			$madatemysql=$madate[2]."-".$madate[1]."-".$madate[0]." 00:00:01";
			$contenuaccueil->datedebut = $madatemysql;
      	}
		else $contenuaccueil->datedebut="0000-00-00 00:00:00";
      
      	if($_REQUEST['datefin']!="") {
			$madate=explode("/",$_REQUEST['datefin']);
			$madatemysql=$madate[2]."-".$madate[1]."-".$madate[0]." 23:59:59";
			$contenuaccueil->datefin = $madatemysql;
      	}
      	else $contenuaccueil->datefin="0000-00-00 00:00:00";
      
      	if($contenuaccueil->datedebut==""&&$contenuaccueil->datepublication=="") $contenuaccueil->datepublication=date("Y-m-d")." 00:00:01";
      	else $contenuaccueil->datepublication=$contenuaccueil->datedebut;
      
      	if($nouveau) {
	        $contenuaccueil->contenu=$contenu->id;
     	   	$contenuaccueil->add();
      	}
      	else $contenuaccueil->maj();
    
    }
    
    function convertirDate($date,$format="") {
    
         if($date=="0000-00-00 00:00:00") return "";
    
         if($format=="") $dateconvertie=date("d/m/Y",strtotime($date));
         else $dateconvertie=date($format,strtotime($date));
         
         return $dateconvertie;
    }
    
    function boucle($texte, $args) {
    
      	$visible = lireTag($args, "visible");
      	$dossier = lireTag($args, "dossier");
      	$classement = lireTag($args, "classement");  
      	$toutes = lireTag($args, "toutes");  
      
      	$contenu = new Contenu();
      
      	$search="";
      	$join="";
      	$order="";
      
      	if($toutes=="1") $toutes=true;
      	else $toutes=false;
      
      	if($visible!="") $search .="AND $this->table.visible=\"$visible\" ";
      
      	if($dossier!="") {
        	$join .="LEFT JOIN $contenu->table ON $this->table.contenu=$contenu->table.id ";
        	$search .="AND $contenu->table.dossier=\"$dossier\" ";
      	}
      
      	if($classement=="datepublication") $order=" ORDER BY $this->table.datepublication";
		if($classement=="datepublicationinverse") $order=" ORDER BY $this->table.datepublication DESC";
      
      	$res="";
      
      	$query = "SELECT $this->table.visible,$this->table.contenu,$this->table.datepublication,$this->table.datedebut,$this->table.datefin
				  FROM $this->table $join
				  WHERE 1 $search $order";
      
      	$resul = $this->query($query);

		if($resul) {
			$nbres = $this->num_rows($resul);
			if ($nbres > 0) {
        		$compteur=0;
          
          		while($row = $this->fetch_object($resul)) {
                	$temp = $texte;
            		if ($toutes||(($row->datedebut=="0000-00-00 00:00:00"&&$row->datefin=="0000-00-00 00:00:00")
						||(strtotime($row->datedebut)<=time()&&strtotime($row->datefin)>=time()))) {
            
              			$compteur=$compteur+1;
            
						$temp = str_replace("#VISIBLE", $row->visible, $temp);
						$temp = str_replace("#CONTENU", $row->contenu, $temp);
						$temp = str_replace("#DATEPUBLICATION", $this->convertirDate($row->datepublication,"Y-m-d"), $temp); 
						$temp = str_replace("#DATEDEBUT", $this->convertirDate($row->datedebut), $temp); 
						$temp = str_replace("#DATEFIN", $this->convertirDate($row->datefin), $temp);
						$temp = str_replace("#COMPT", $compteur, $temp);
					  
						$res .= $temp;
            
            		}          
          		}
          
        	}
      	}
      
      	return $res;      
    }

}
?>