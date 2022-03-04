<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

class Produitsassocies extends PluginsClassiques
{
    public $id;
    public $id_objet;
    public $type;
    public $id_produit;
    public $classement;

    const TABLE = "produitsassocies";

    public $table = self::TABLE;

    public $bddvars = array("id", "id_objet", "type", "id_produit", "classement");

    function Produitsassocies()
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
    			  `id_produit`  INT(11) NOT NULL,
            `classement` INT(11) NOT NULL,
    			  PRIMARY KEY  (`id`)
  			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
        ;
        $this->query($query, $cnx->link);
    }

    function charger($id = null, $var2 = null)
    {
        if ($id != null) return $this->getVars('SELECT * FROM '.self::TABLE.' WHERE id="'.$id.'"');
    }

    function charger_produits($type, $id)
    {
        $resul = CacheBase::getCache()->query('SELECT * FROM '.self::TABLE.' WHERE type="'.$type.'" AND id_objet="'.$id.'" ORDER BY classement');

        if (empty($resul)) return "";

        $produits=[];

        foreach ($resul as $row) {
            $produits[] = $row->id_produit;
        }

        return $produits;
    }

    function sup_produits($type,$id)
    {
        $produitsassocies = new Produitsassocies();
        $query = "delete from $produitsassocies->table where type=\"$type\" and id_objet=\"$id\"";
        if ($id != "") {
            $resul = $produitsassocies->query($query);
            CacheBase::getCache()->reset_cache();
        }
    }

    function supcont($contenu)
    {
        $produitsassocies = new Produitsassocies();
        $query = "delete from $produitsassocies->table where type=\"contenu\" and id_objet=\"$contenu->id\"";
        if ($contenu->id != ""){
            $resul = $produitsassocies->query($query);
            CacheBase::getCache()->reset_cache();
        }
    }

    function boucle($texte, $args)
    {
        // récupération des arguments
  			$type=lireTag($args, "type_objet");
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

        $produitsassocies=new Produitsassocies();

  			$query = "select * from $produitsassocies->table where 1 $search $order $limit;";
  			$resul = mysql_query($query, $produitsassocies->link);
  			$nbres = mysql_numrows($resul);
  			if(!$nbres) return "";

        $compt=1;
  			while( $row = mysql_fetch_object($resul)) {
    				$temp = str_replace("#ID_PRODUIT", $row->id_produit, $texte);
            $temp = str_replace("#NBRES", $nbres, $temp);
            $temp = str_replace("#COMPT", $compt, $temp);
    				$res .= $temp;
            $compt=$compt+1;
  			}

  			return $res;
    }
}

?>
