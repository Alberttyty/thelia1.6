<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");

class Dc_produitsuivprec extends PluginsClassiques
{
		function dc_produitsuivprec()
		{
				$this->PluginsClassiques("dc_produitsuivprec");
		}

		function init()
		{
				// TITRE - CHAPO - DESCRIPTION - DEVISE
				$this->ajout_desc("Produit Suivant/Pr&eacute;d&eacute;dent","Plugin Produit Suivant/Pr&eacute;c&eacute;dent","Ce plugin permet de connaitre le produit pr&eacute;c&eacute;dent et le produit suivant.",1);
		}

		function destroy() {}

		function boucle($texte, $args)
		{
				$i=0;
				// récupération des arguments
				$ref        = lireTag($args, "ref");
				$suivant   	= lireTag($args, "suivant");
				$precedent 	= lireTag($args, "precedent");
				$rubrique   = lireTag($args, "rubrique");
				$classement = lireTag($args, "classement");

				// préparation de la requete
				if($classement == "prixmin") $order = "order by "  . " prix";
				else if($classement == "prixmax") $order = "order by "  . " prix desc";
				else if($classement == "rubrique") $order = "order by "  . " rubrique";
				else if($classement == "manuel") $order = "order by classement";
				else if($classement == "inverse") $order = "order by classement desc";
				else if($classement == "date") $order = "order by datemodif desc";
				else if($classement == "titre") $order = "order by titre";
		    else if($classement == "titreinverse") $order = "order by produitdesc.titre desc";
				else $order = "order by classement";

				$search		= "";
				$res		= "";

				if (!empty($rubrique)) $search1 = "rubrique in($rubrique)";

				//requete
				if($classement != "titre" && $classement != "titreinverse"){
						$query_produit = "SELECT ref FROM produit WHERE ligne='1' AND $search1 $order";
				}
				else {
						$query_produit = "SELECT produit.ref FROM produit, produitdesc WHERE ligne='1' AND produit.id=produitdesc.produit AND $search1 $order";
				}
				$resul_produit = mysql_query($query_produit);
				$nb_produit = mysql_num_rows($resul_produit);

				while($row = mysql_fetch_assoc($resul_produit)) {
						if($row["ref"] != $ref) $i++;
						else break;
				}

				//récupération de la position de l'élèment suivant et précédent
				$posprec = $i-1;
				$possuiv = $i+1;

				//test si la boucle est pour le produit précédent
				if ($precedent=="1") {
						if ($posprec >= 0) {
								if ($classement != "titre" && $classement != "titreinverse") {
										$query_prec = "SELECT ref FROM produit WHERE ligne='1' AND $search1 $order limit $posprec,1";
					    	}
								else {
										$query_prec = "SELECT produit.ref FROM produit, produitdesc WHERE ligne='1' AND produit.id=produitdesc.produit AND $search1 $order limit $posprec,1";
								}
								$resul_prec = mysql_query($query_prec);
								$prod_prec  = mysql_result($resul_prec, 0);
						}
						else return "";
				}
				//test si la boucle est pour le produit suivant
				if ($suivant=="1"){
						if ($possuiv < $nb_produit) {
								if ($classement != "titre" && $classement != "titreinverse") {
										$query_suiv = "SELECT ref FROM produit WHERE ligne='1' AND $search1 $order limit $possuiv,1";
					    	}
								else {
										$query_suiv = "SELECT produit.ref FROM produit, produitdesc WHERE ligne='1' AND produit.id=produitdesc.produit AND $search1 $order limit $possuiv,1";
								}

								$resul_suiv = mysql_query($query_suiv);
								$prod_suiv = mysql_result($resul_suiv, 0);
						}
						else return "";
				}

				//renvoie des données
				$temp = str_replace("#REFPREC", "$prod_prec", $texte);
				$temp = str_replace("#REFSUIV", "$prod_suiv", $temp);

				$res .= $temp;

				return $res;
		}

		function action() {}
}
?>
