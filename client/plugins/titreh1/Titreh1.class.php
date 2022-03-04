<?php

include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

class Titreh1 extends PluginsClassiques
{
    var $id;
    var $id_objet;
    var $type;
    var $titre;
    var $lang;

    const TABLE = "titreh1";

    var $table = self::TABLE;

    var $bddvars = array("id", "id_objet", "type", "titre", "lang");

    function Titreh1()
    {
        $this->PluginsClassiques();
    }

    function init()
    {
        $cnx = new Cnx();
        $query = "CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `id_objet` INT(11) NOT NULL,
			  `type` VARCHAR(25) NOT NULL,
			  `titre` VARCHAR(255) NOT NULL,
        `lang` INT(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->query($query, $cnx->link);
    }
    
    function charger_objet($type,$id,$lang)
    {
    
        if($lang=="") $lang=1;
    
        $requete = "select * from ".self::TABLE." where type=\"".$type."\" AND id_objet=\"".$id."\" AND lang=\"".$lang."\"";

        return $this->getVars($requete);
    }
    
    function modrub($rubrique){
    
      $titreh1 = new Titreh1();
      $titreh1->type="rubrique";
      $titreh1->id_objet=$rubrique->id;
      $titreh1->lang=$_REQUEST['lang'];
      
      if($titreh1->charger_objet($titreh1->type,$titreh1->id_objet,$titreh1->lang)) $nouveau=false;
      else $nouveau=true;
      
      $titreh1->titre = $_REQUEST['titreh1_titre'];
      $titreh1->titre = str_replace(array("'",'"'),array('&#039;', '&#034;'), $titreh1->titre);
      
      if($nouveau) $titreh1->add();
      else $titreh1->maj();
    
    }
    
    function suprub($rubrique){
      $titreh1 = new Titreh1();
      $query = "delete from $titreh1->table where type=\"rubrique\" and id_objet=\"$rubrique->id\"";
      if($rubrique->id != ""){
        $resul = $titreh1->query($query);
        CacheBase::getCache()->reset_cache();
      }
    }
    
    function modprod($produit){
    
      $titreh1 = new Titreh1();
      $titreh1->type="produit";
      $titreh1->id_objet=$produit->id;
      $titreh1->lang=$_REQUEST['lang'];
      
      if($titreh1->charger_objet($titreh1->type,$titreh1->id_objet,$titreh1->lang)) $nouveau=false;
      else $nouveau=true;
      
      $titreh1->titre = $_REQUEST['titreh1_titre'];
      $titreh1->titre = str_replace(array("'",'"'),array('&#039;', '&#034;'), $titreh1->titre);
      
      if($nouveau) $titreh1->add();
      else $titreh1->maj();
    
    }
    
    function supprod($produit){
      $titreh1 = new Titreh1();
      $query = "delete from $titreh1->table where type=\"produit\" and id_objet=\"$produit->id\"";
      if($produit->id != ""){
        $resul = $titreh1->query($query);
        CacheBase::getCache()->reset_cache();
      }
    }
    
    function moddos($dossier){
    
      $titreh1 = new Titreh1();
      $titreh1->type="dossier";
      $titreh1->id_objet=$dossier->id;
      $titreh1->lang=$_REQUEST['lang'];
      
      if($titreh1->charger_objet($titreh1->type,$titreh1->id_objet,$titreh1->lang)) $nouveau=false;
      else $nouveau=true;
      
      $titreh1->titre = $_REQUEST['titreh1_titre'];
      $titreh1->titre = str_replace(array("'",'"'),array('&#039;', '&#034;'), $titreh1->titre);
      
      if($nouveau) $titreh1->add();
      else $titreh1->maj();
    
    }
    
    function supdos($dossier){
      $titreh1 = new Titreh1();
      $query = "delete from $titreh1->table where type=\"dossier\" and id_objet=\"$dossier->id\"";
      if($dossier->id != ""){
        $resul = $titreh1->query($query);
        CacheBase::getCache()->reset_cache();
      }
    }
    
    function modcont($contenu){
    
      $titreh1 = new Titreh1();
      $titreh1->type="contenu";
      $titreh1->id_objet=$contenu->id;
      $titreh1->lang=$_REQUEST['lang'];
      
      if($titreh1->charger_objet($titreh1->type,$titreh1->id_objet,$titreh1->lang)) $nouveau=false;
      else $nouveau=true;
      
      $titreh1->titre = $_REQUEST['titreh1_titre'];
      $titreh1->titre = str_replace(array("'",'"'),array('&#039;', '&#034;'), $titreh1->titre);
      
      if($nouveau) $titreh1->add();
      else $titreh1->maj();
    
    }
    
    function supcont($contenu){
      $titreh1 = new Titreh1();
      $query = "delete from $titreh1->table where type=\"contenu\" and id_objet=\"$contenu->id\"";
      if($contenu->id != ""){
        $resul = $titreh1->query($query);
        CacheBase::getCache()->reset_cache();
      }
    }

    function boucle($texte, $args){
    
      // récupération des arguments
			$type=lireTag($args, "type");
			$id_objet=lireTag($args, "id_objet");
			
      if (isset($_SESSION["navig"]->lang)) $lang=$_SESSION["navig"]->lang;
      else $lang = 1;

			$search ="";
			$res="";
			$order="";
      $limit="";

			// préparation de la requête
			if($type!="")  $search.=" and type=\"$type\"";
			if($id_objet!="")  $search.=" and id_objet=\"$id_objet\"";
			if($lang!="")  $search.=" and lang=\"$lang\"";
      
      $titreh1=new Titreh1();

			$query = "select * from $titreh1->table where 1 $search $order $limit;";
			$resul = mysql_query($query, $titreh1->link);
			$nbres = mysql_numrows($resul);
			if(!$nbres) return "";

			while( $row = mysql_fetch_object($resul)){
				$temp = str_replace("#TITRE", "$row->titre", $texte);
				$res .= $temp;
			}

			return $res;
       
    }
    
    function action(){
    
      global $res,$fond;
                                                                                   
      $titreh1 = new Titreh1();
      
      if (isset($_SESSION["navig"]->lang)) $lang=$_SESSION["navig"]->lang;
      else $lang = 1;
      
      //RUBRIQUE
      if (isset($_REQUEST['id_rubrique'])&&!isset($_REQUEST['id_produit'])){ 
        $titreh1->charger_objet('rubrique',$_REQUEST['id_rubrique'],$lang);
        $res = str_replace("#TITREH1", $titreh1->titre, $res);
      }
      
      // PRODUIT
      if (isset($_REQUEST['id_produit'])){
        $titreh1->charger_objet('produit',$_REQUEST['id_produit'],$lang);
        $res = str_replace("#TITREH1", $titreh1->titre, $res);
      }
      
      // DOSSIER
      if (isset($_REQUEST['id_dossier'])&&!isset($_REQUEST['id_contenu'])){
        $titreh1->charger_objet('dossier',$_REQUEST['id_dossier'],$lang);
        $res = str_replace("#TITREH1", $titreh1->titre, $res);
      }
      
      // CONTENU
      if (isset($_REQUEST['id_contenu'])){
        $titreh1->charger_objet('contenu',$_REQUEST['id_contenu'],$lang);
        $res = str_replace("#TITREH1", $titreh1->titre, $res);
      }
    
    }

}

?>