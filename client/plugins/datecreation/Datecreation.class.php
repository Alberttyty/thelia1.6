<?php

include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

class Datecreation extends PluginsClassiques
{
    var $id;
    var $id_objet;
    var $type;
    var $date;

    const TABLE = "datecreation";

    var $table = self::TABLE;

    var $bddvars = array("id", "id_objet", "type", "date");

    function Datecreation()
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
			  `date` datetime NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->query($query, $cnx->link);
    }

    function charger_objet($type,$id)
    {
        $requete = "select * from ".self::TABLE." where type=\"".$type."\" AND id_objet=\"".$id."\"";
        return $this->getVars($requete);
    }

    function modrub($rubrique){

      $datecreation = new Datecreation();
      $datecreation->type="rubrique";
      $datecreation->id_objet=$rubrique->id;

      if($datecreation->charger_objet($datecreation->type,$datecreation->id_objet)) $nouveau=false;
      else $nouveau=true;

      if($nouveau){
        $datecreation->date=date("Y-m-d H:i:s");
        $datecreation->add();
      }

    }

    function suprub($rubrique){
      $datecreation = new Datecreation();
      $query = "delete from $datecreation->table where type=\"rubrique\" and id_objet=\"$rubrique->id\"";
      if($rubrique->id != ""){
        $resul = $datecreation->query($query);
        CacheBase::getCache()->reset_cache();
      }
    }

    function modprod($produit){

      $datecreation = new Datecreation();
      $datecreation->type="produit";
      $datecreation->id_objet=$produit->id;

      if($datecreation->charger_objet($datecreation->type,$datecreation->id_objet)) $nouveau=false;
      else $nouveau=true;

      if($nouveau){
        $datecreation->date=date("Y-m-d H:i:s");
        $datecreation->add();
      }

    }

    function supprod($produit){
      $datecreation = new Datecreation();
      $query = "delete from $datecreation->table where type=\"produit\" and id_objet=\"$produit->id\"";
      if($produit->id != ""){
        $resul = $datecreation->query($query);
        CacheBase::getCache()->reset_cache();
      }
    }

    function moddos($dossier){

      $datecreation = new Datecreation();
      $datecreation->type="dossier";
      $datecreation->id_objet=$dossier->id;

      if($datecreation->charger_objet($datecreation->type,$datecreation->id_objet)) $nouveau=false;
      else $nouveau=true;

      if($nouveau){
        $datecreation->date=date("Y-m-d H:i:s");
        $datecreation->add();
      }

    }

    function supdos($dossier){
      $datecreation = new Datecreation();
      $query = "delete from $datecreation->table where type=\"dossier\" and id_objet=\"$dossier->id\"";
      if($dossier->id != ""){
        $resul = $datecreation->query($query);
        CacheBase::getCache()->reset_cache();
      }
    }

    function modcont($contenu){

      $datecreation = new Datecreation();
      $datecreation->type="contenu";
      $datecreation->id_objet=$contenu->id;

      if($datecreation->charger_objet($datecreation->type,$datecreation->id_objet)) $nouveau=false;
      else $nouveau=true;

      if($nouveau){
        $datecreation->date=date("Y-m-d H:i:s");
        $datecreation->add();
      }

    }

    function supcont($contenu){
      $datecreation = new Datecreation();
      $query = "delete from $datecreation->table where type=\"contenu\" and id_objet=\"$contenu->id\"";
      if($contenu->id != ""){
        $resul = $datecreation->query($query);
        CacheBase::getCache()->reset_cache();
      }
    }

    function boucle($texte, $args){

      // récupération des arguments
			$type=lireTag($args, "type");
			$id_objet=lireTag($args, "id_objet");

			$search ="";
			$res="";
			$order="";
      $limit="";

			// préparation de la requête
			if($type!="")  $search.=" and type=\"$type\"";
			if($id_objet!="")  $search.=" and id_objet=\"$id_objet\"";

      $datecreation=new Datecreation();

			$query = "select * from $datecreation->table where 1 $search $order $limit;";
			$resul = mysql_query($query, $titreh1->link);
			$nbres = mysql_numrows($resul);
			if(!$nbres) return "";

			while( $row = mysql_fetch_object($resul)){
				$temp = str_replace("#DATE", date("Y-m-d",strtotime($row->date)), $texte);
				$res .= $temp;
			}

			return $res;

    }

    function action(){

      global $res,$fond;

      $datecreation = new Datecreation();

      //RUBRIQUE
      if (isset($_REQUEST['id_rubrique'])&&!isset($_REQUEST['id_produit'])){
        $datecreation->charger_objet('rubrique',$_REQUEST['id_rubrique']);
        $res = str_replace("#DATECREATION", date("Y-m-d",strtotime($datecreation->date)), $res);
      }

      // PRODUIT
      if (isset($_REQUEST['id_produit'])){
        $datecreation->charger_objet('produit',$_REQUEST['id_produit']);
        $res = str_replace("#DATECREATION", date("Y-m-d",strtotime($datecreation->date)), $res);
      }

      // DOSSIER
      if (isset($_REQUEST['id_dossier'])&&!isset($_REQUEST['id_contenu'])){
        $datecreation->charger_objet('dossier',$_REQUEST['id_dossier']);
        $res = str_replace("#DATECREATION", date("Y-m-d",strtotime($datecreation->date)), $res);
      }

      // CONTENU
      if (isset($_REQUEST['id_contenu'])){
        $datecreation->charger_objet('contenu',$_REQUEST['id_contenu']);
        $res = str_replace("#DATECREATION", date("Y-m-d",strtotime($datecreation->date)), $res);
      }

    }

}

?>
