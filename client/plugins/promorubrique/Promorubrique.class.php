<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Promo.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubrique.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

class Promorubrique extends PluginsClassiques
{
    public $id;
    public $id_promo;
    public $rubriques;

    public $table="promorubrique";
		public $bddvars = array("id", "id_promo", "rubriques");

		function Promorubrique()
    {
			   $this->PluginsClassiques();
		}

    function init()
    {
  		  $this->ajout_desc("Promo par rubrique", "Code promo utilisable pour une rubrique", "", 1);
        $cnx = new Cnx();
  			$query = "CREATE TABLE `promorubrique` (
    			  `id` int(11) NOT NULL auto_increment,
    			  `id_promo` int(11) NOT NULL,
    			  `rubriques` text NOT NULL,
    			  PRIMARY KEY  (`id`)
  			) AUTO_INCREMENT=1 ;"
        ;
  			$resul = mysql_query($query, $cnx->link);
    }

    function charger($id_promo = null, $var2 = null)
    {
			   if ($id_promo != null) return $this->getVars("SELECT * FROM $this->table WHERE id_promo=\"$id_promo\"");
		}

    function aprespromo($code)
    {
        $promo=new Promo();
        $promo->charger($code);

        if ($this->charger($promo->id)&&$promo->code!="") {
            if (!$this->rubriquePresente(explode(",",$this->rubriques))) {
                /* Si la rubrique du code promo n'est pas présente dans le panier, on remet la remise à zéro */
                $_SESSION['navig']->promo=new Promo();
            }
        }
    }

    /* On vérifie le montant avant d'appliquer la remise */
    function calc_remise(&$remise,$total)
    {
        $promo = &$_SESSION['navig']->promo;

        // si le code promo existe dans la table des codes promo avec un rubrique de spécifiée
        if ($this->charger($promo->id)&&$promo->code!="") {
            if(!$this->rubriquePresente(explode(",",$this->rubriques))) {
                /* Si la rubrique du code promo n'est pas présente dans le panier, on remet la remise à zéro */
                $_SESSION['navig']->promo=new Promo();
                $remise=0;
            }

            $total_rubriques=0;

            foreach(explode(",",$this->rubriques) as $key => $rubrique) {
                $total_rubriques+=$this->totalRubrique($rubrique);
            }

            /* Si la remise est plus grande que le total de la rubrique, on ajuste */
            if ($this->rubriques!=""&&$remise>$total_rubriques) $remise=$total_rubriques;
        }
    }

    /* Test si la rubrique de la promo est présente */
    function rubriquePresente($rubriques)
    {
        $presente=false;
        foreach($_SESSION['navig']->panier->tabarticle as $article){
            if (in_array($article->produit->rubrique,$rubriques)) $presente=true;
        }
        return $presente;
    }

    /* Montant total pour une rubrique dans le panier */
    function totalRubrique($rubrique)
    {
        $total_rubrique=0;

        foreach($_SESSION['navig']->panier->tabarticle as $article){
           if ($article->produit->rubrique==$rubrique){
              if($article->produit->promo==1) $prix=$article->produit->prix2;
              else $prix=$article->produit->prix;
              $total_rubrique+=$prix*$article->quantite;
           }
        }

        return $total_rubrique;
    }
    /*
    function afficherChemin($rubrique_id,$restart=false)
    {
        static $chemin=array();

        if ($restart) $chemin=array();

        $rubrique=new Rubrique();
        $rubrique->charger($rubrique_id);

        if ($rubrique->parent!=0) {
            $chemin[]=$rubrique->parent;
            $this->afficherChemin($rubrique->parent);
        }

        return $chemin;
    }*/

    function ajoutpromo($promo)
    {
        $promo->charger($promo->code);
        $this->majpromo($promo);
    }

    function majpromo($promo)
    {
        $promorubrique = new Promorubrique();

        if ($promorubrique->charger($promo->id)) $nouveau=false;
        else $nouveau=true;

        $promorubrique->id_promo=$promo->id;

        // si on désactive
        if ($_REQUEST['promorubrique_actif']=="non"){
            unset($_REQUEST['promorubrique']);
            $promorubrique->rubriques="";
        }

        // si au moins une rurbique
        if ((isset($_REQUEST['promorubrique'])&&count($_REQUEST['promorubrique'])>=1)) {
            $promorubrique->rubriques=implode(",",$_REQUEST['promorubrique']);
        }

        // si pas de rubrique on supprime
        if ((!isset($_REQUEST['promorubrique']))||count($_REQUEST['promorubrique'])<1){
            if(!$nouveau) $promorubrique->delete();
        }
        // si au moin une rubrique au sauvegarde
        else {
            if($nouveau) $promorubrique->add();
            else $promorubrique->maj();
        }
    }

    function suppromo($promo)
    {
        $promorubrique = new Promorubrique();
        $promorubrique->charger($promo->id);
        $promorubrique->delete();
    }

    function rubriqueAvecProduit($rubrique_id)
    {
        $query = "SELECT id FROM ".Produit::TABLE." WHERE rubrique=".$rubrique_id." LIMIT 0,1";
        $resul = $this->query($query);

        if ($this->num_rows($resul)>0) return true;
        else return false;
    }

    function rubriqueAvecEnfants($rubrique_id)
    {
        $query = "SELECT id FROM ".Rubrique::TABLE." WHERE parent=".$rubrique_id." LIMIT 0,1";
        $resul = $this->query($query);

        if ($this->num_rows($resul)>0) return true;
        else return false;
    }
}
?>
