<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

class Dc_meilleursventes extends PluginsClassiques {

	const VERSION 	= '1.3.1';
	const MODULE    = 'Meilleurs ventes';

	public function __construct() {
		parent::__construct("dc_meilleursventes");
	}

	public function init() {
		// TITRE - CHAPO - DESCRIPTION - DEVISE
		$this->ajout_desc(
           	"Meilleurs ventes",
            "Plugin de meilleurs ventes",
            "Ce plugin vous permet d'afficher les meilleurs ventes de vos produit.",
            1);
	}

	public function destroy() {
	}

	public function boucle($texte, $args) {

		$num = lireTag($args, "num");
		$classement = lireTag($args, "classement");
		$exclusion = lireTag($args, "exclusion");
		$rub = lireTag($args, "rubrique");
		$payee = lireTag($args, "payee");
		$depuis = lireTag($args, "depuis");
		$enligne = lireTag($args, "enligne", "int");

		$where = "";
		$res = "";

		if($classement == "inverse") $ordre = "ASC";
		else $ordre = "DESC";

		if($exclusion != "") $where .= " AND v.ref NOT IN('" . str_replace(",", "','", $exclusion) . "')";
		if($rub != "") $where .= " AND p.rubrique IN('" . str_replace(",", "','", $rub) . "')";
       	if($payee == "1") $where .= " AND c.statut IN(2,3,4)";
      	if($enligne == "1") $search .= " AND p.ligne=1";
      
      	if($depuis != "") {
        	$datedepuis = date("Y-m-d",strtotime("-".$depuis." days")); 
        	$datedepuis .= ' 00:00:01';
			$where .= " AND c.date > '".$datedepuis."'";
		}

		$query = "SELECT count(v.ref) AS totalvente, v.ref, v.titre, p.id
				  FROM ".Commande::TABLE." AS c
				  LEFT JOIN ".Venteprod::TABLE." AS v ON v.commande = c.id
				  LEFT JOIN ".Produit::TABLE." p on p.ref = v.ref
				  WHERE v.ref<> '' $where
				  GROUP BY v.ref
				  ORDER BY totalvente $ordre
				  LIMIT 0,$num
				  ";
		$resul = $this->query($query);

      	$compteur=0;
		while ($resul && $row = $this->fetch_object($resul)) {
        	$compteur=$compteur+1;

			$temp = $texte;

			$temp = str_replace("#PRODUIT", "$row->id", $temp);
			$temp = str_replace("#REF", 	"$row->ref", $temp);
			$temp = str_replace("#TITRE", 	"$row->titre", $temp);
			$temp = str_replace("#NB","$row->totalvente", $temp);
        	$temp = str_replace("#COMPTEUR", "$compteur", $temp); 

			$res .= $temp;
		}

		return $res;
	}
}
?>