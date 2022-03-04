<?php
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");

class Quantitestock extends PluginsClassiques
{
		function Quantitestock()
		{
				$this->PluginsClassiques();
		}

    function boucle($texte, $args)
		{
				// récupération des arguments
				$res="";
				$produit="";
				$declidisp="";

				$article = lireTag($args, "article");
				$max = lireTag($args, "max");
				$ref = lireTag($args, "ref");
				$maxpanier = lireTag($args, "maxpanier");
				$quantiteextra=lireTag($args, "quantiteextra");
				$declinaison=lireTag($args, "declinaison");
				$forcerstock=lireTag($args, "forcerstock");

				$produit = new Produit();
		    if($article != "") $produit->charger($_SESSION['navig']->panier->tabarticle[$article]->produit->ref);
				else if($ref != "") {
		        $produit->charger($ref);
						$article=0;
				}

				if ($declinaison=="1") {
						if(isset($_SESSION['navig']->panier->tabarticle[$article]->perso[0])) $declidisp=$_SESSION['navig']->panier->tabarticle[$article]->perso[0]->valeur;
						if($declidisp == "" || $produit == "") { }
						else {
								$stock = new Stock();
								$stock->charger($declidisp, $produit->id);
								if($max!=""){if($stock->valeur<$max){$max=$stock->valeur;}}
						}
				}
				else {
						$stock=$produit->stock;
						if($max!=""){if($stock<$max){$max=$stock;}}
				}

				if($forcerstock != "" && $forcerstock!=0) $max = $forcerstock;
				if($max == "") $max = 10;

				for($i=1; $i<=$max; $i++) {
						if($i==$_SESSION['navig']->panier->tabarticle[$article]->quantite) $selected="selected=\"selected\"";
						else $selected="";

						$temp = str_replace("#NUM", "$i", $texte);
						$temp = str_replace("#SELECTED", $selected, $temp);

						$res.="$temp";
				}

				if($quantiteextra!="" && $quantiteextra>$max) {
						if($quantiteextra==$_SESSION['navig']->panier->tabarticle[$article]->quantite) $selected="selected=\"selected\"";
						else $selected="";

						$temp = str_replace("#NUM", $quantiteextra, $texte);
						$temp = str_replace("#SELECTED", $selected, $temp);

						$res.="$temp";
				}

				return $res;
		}
}
?>
