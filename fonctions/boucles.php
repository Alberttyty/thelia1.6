<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
/* Gestion des boucles */
require_once(__DIR__ . "/divers.php");
require_once(__DIR__ . "/../lib/simplepie.inc");

/* Gestion des boucles de type Rubrique*/
function boucleRubrique($texte, $args) {
    global $id_rubrique;
    // récupération des arguments
    $id = lireTag($args, "id", "int_liste");
    $parent = lireTag($args, "parent", "int_liste");
    $courante = lireTag($args, "courante", "int");
    $pasvide = lireTag($args, "pasvide", "int");
    $ligne = lireTag($args, "ligne", "int");
    $lien = lireTag($args, "lien", "string+\/-\s\.\,;");
    $classement = lireTag($args, "classement", "string");
    $aleatoire = lireTag($args, "aleatoire", "int");
    $exclusion = lireTag($args, "exclusion", "int_liste");
    $deb = lireTag($args, "deb", "int");
    $num = lireTag($args, "num", "int");
    $niveau = lireTag($args, "niveau", "int");

    $res = "";
    $search = "";
    $limit = "";

    if (!$deb) $deb = 0;

    $rubrique = new Rubrique();
    $rubriquedesc = new Rubriquedesc();

    // preparation de la requete

    if ($ligne == "") $ligne = "1";

    $search .= " and $rubrique->table.ligne=\"$ligne\"";

    if ($id != "") $search .= " and $rubrique->table.id in ($id)";
    if ($parent != "") $search .= " and $rubrique->table.parent in ($parent)";
    if ($courante == "1") $search .= " and $rubrique->table.id='$id_rubrique'";
    else if ($courante == "0") $search .= " and $rubrique->table.id!='$id_rubrique'";
    if ($num != "") $limit .= " limit $deb,$num";
    if ($exclusion != "") $search .= " and $rubrique->table.id not in($exclusion)";
    if ($lien != "") $search .= " and $rubrique->table.lien in ($lien)";

    if ($niveau != "" && $parent != "") {
        if ($id_rubrique == "")
            return "";

        $tab = chemin_rub($id_rubrique);

        $trouve = 0;

        for ($i = 0; $i < count($tab); $i++)
            if ($parent == $tab[$i]->rubrique)
                $trouve = 1;

        if (!$trouve)
            return "";
    }


    if (ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE) {
        // On retourne uniquement les rubriques traduites
        $search .= " and $rubriquedesc->table.id is not null";
    }

    if ($aleatoire) $order = "order by " . " RAND()";
    else if ($classement == "alpha") $order = "order by $rubriquedesc->table.titre";
    else if ($classement == "alphainv") $order = "order by $rubriquedesc->table.titre desc";
    else if ($classement == "inverse") $order = "order by $rubrique->table.classement desc";
    else $order = "order by $rubrique->table.classement";

    $query = "
			select
				$rubrique->table.id
			from
				$rubrique->table
			left join
				$rubriquedesc->table on ($rubrique->table.id=$rubriquedesc->table.rubrique and $rubriquedesc->table.lang=" . ActionsLang::instance()->get_id_langue_courante() . ")
			where
				1 $search
			$order
			$limit";

    $resul = CacheBase::getCache()->query($query);

    $compt = 1;


    if (empty($resul)) return ""; else $nbres = count($resul);

    foreach ($resul as $row) {
        $rubrique = new Rubrique();
        $rubrique->charger($row->id);
        $nbenfant = 0;

        if ($pasvide == "1") {
            $rec = arbreBoucle($rubrique->id);
            $rec = rtrim($rec, ",");
            if ($rec) $virg = ",";
            else $virg = "";

            $tmprod = new Produit();
            $query4 = "select count(id) as nbres from $tmprod->table where rubrique in('" . $rubrique->id . "'$virg$rec) and ligne='1'";
            $resul4 = CacheBase::getCache()->query($query4);
            if (!$resul4[0]->nbres) continue;

        }


        $rubriquedesc = new Rubriquedesc();
        $rubriquedesc->charger($rubrique->id);

        $query3 = "select count(id) as nbres from $rubrique->table where 1 and parent=\"$rubrique->id\"";
        $resul3 = CacheBase::getCache()->query($query3);
        if ($resul3[0]->nbres) $nbenfant = $resul3[0]->nbres;
        else $nbenfant = 0;

        $temp = str_replace("#TITRE", "$rubriquedesc->titre", $texte);
        $temp = str_replace("#STRIPTITRE", strip_tags($rubriquedesc->titre), $temp);
        $temp = str_replace("#CHAPO", "$rubriquedesc->chapo", $temp);
        $temp = str_replace("#STRIPCHAPO", strip_tags($rubriquedesc->chapo), $temp);
        $temp = str_replace("#DESCRIPTION", "$rubriquedesc->description", $temp);
        $temp = str_replace("#POSTSCRIPTUM", "$rubriquedesc->postscriptum", $temp);
        $temp = str_replace("#PARENT", "$rubrique->parent", $temp);
        $temp = str_replace("#ID", "$rubrique->id", $temp);
        $temp = str_replace("#URL", $rubriquedesc->getUrl(), $temp);
        $temp = str_replace("#REWRITEURL", $rubriquedesc->getUrl(), $temp);
        $temp = str_replace("#LIEN", "$rubrique->lien", $temp);
        $temp = str_replace("#COMPT", "$compt", $temp);
        $temp = str_replace("#NBRES", "$nbres", $temp);
        $temp = str_replace("#NBENFANT", "$nbenfant", $temp);


        $compt++;

        if (trim($temp) != "") $res .= $temp;

    }


    return $res;


}

/* Gestion des boucles de type Dossier*/
function boucleDossier($texte, $args) {

    global $id_dossier;

    // récupération des arguments
    $id = lireTag($args, "id", "int_list");
    $parent = lireTag($args, "parent", "int_list");
    $deb = lireTag($args, "deb", "int");
    $num = lireTag($args, "num", "int");
    $courant = lireTag($args, "courant", "int");
    $ligne = lireTag($args, "ligne", "int");
    $lien = lireTag($args, "lien", "string+\/-\s\.\,;");
    $aleatoire = lireTag($args, "aleatoire", "int");
    $classement = lireTag($args, "classement", "string");
    $exclusion = lireTag($args, "exclusion", "int_liste");

    $search = "";
    $res = "";
    $limit = "";

    if (!$deb) $deb = 0;

    if ($ligne == "") $ligne = "1";

    // preparation de la requete
    $search .= " and ligne='$ligne'";
    if ($id != "") $search .= " and id in($id)";
    if ($lien != "") $search .= " and " . Rubrique::TABLE . ".lien in ($lien)";
    if ($parent != "") $search .= " and parent=\"$parent\"";
    if ($courant == "1") $search .= " and id='$id_dossier'";
    else if ($courant == "0") $search .= " and id!='$id_dossier'";
    if ($num != "") $limit .= " limit $deb,$num";
    if ($exclusion != "") $search .= " and id not in($exclusion)";

    $dossier = new Dossier();

    if ($aleatoire) $order = "order by " . " RAND()";
    else if ($classement == "aleatoire") $order = "order by " . " RAND()";
    else if ($classement == "manuel") $order = "order by classement";
    else if ($classement == "inverse") $order = "order by classement desc";
    else $order = "order by classement";

    $query = "select * from $dossier->table where 1 $search $order $limit";


    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return ""; else $nbres = count($resul);


    $dossierdesc = new Dossierdesc();
    $compt = 1;

    foreach ($resul as $row) {
        $dossierdesc = new Dossierdesc();
        if (!$dossierdesc->charger($row->id)) continue;

        $query3 = "select * from $dossier->table where 1 and parent=\"$row->id\"";
        $nbenfant = CacheBase::getCache()->query_count($query3);


        $temp = str_replace("#TITRE", "$dossierdesc->titre", $texte);
        $temp = str_replace("#STRIPTITRE", strip_tags($dossierdesc->titre), $temp);
        $temp = str_replace("#CHAPO", "$dossierdesc->chapo", $temp);
        $temp = str_replace("#STRIPCHAPO", strip_tags($dossierdesc->chapo), $temp);
        $temp = str_replace("#DESCRIPTION", "$dossierdesc->description", $temp);
        $temp = str_replace("#POSTSCRIPTUM", "$dossierdesc->postscriptum", $temp);
        $temp = str_replace("#PARENT", "$row->parent", $temp);
        $temp = str_replace("#ID", "$row->id", $temp);
        $temp = str_replace("#URL", $dossierdesc->getUrl(), $temp);
        $temp = str_replace("#REWRITEURL", $dossierdesc->getUrl(), $temp);
        $temp = str_replace("#LIEN", "$row->lien", $temp);
        $temp = str_replace("#COMPT", "$compt", $temp);
        $temp = str_replace("#NBRES", "$nbres", $temp);
        $temp = str_replace("#NBENFANT", "$nbenfant", $temp);


        $compt++;

        if (trim($temp) != "") $res .= $temp;
    }

    return $res;
}


function boucleImage($texte, $args) {

    // récupération des arguments
    $produit = lireTag($args, "produit", "int");
    $id = lireTag($args, "id", "int");
    $num = lireTag($args, "num", "int");
    $nb = lireTag($args, "nb", "int");
    $debut = lireTag($args, "debut", "int");
    $deb = lireTag($args, "deb", "int");
    $rubrique = lireTag($args, "rubrique", "int");
    $largeur = lireTag($args, "largeur", "int");
    $hauteur = lireTag($args, "hauteur", "int");
    $dossier = lireTag($args, "dossier", "int");
    $contenu = lireTag($args, "contenu", "int");
    $opacite = lireTag($args, "opacite", "int");
    $noiretblanc = lireTag($args, "noiretblanc", "int");
    $miroir = lireTag($args, "miroir", "int");
    $aleatoire = lireTag($args, "aleatoire", "int");
    $exclusion = lireTag($args, "exclusion", "int_liste");
    $exact = lireTag($args, "exact", "int");
    $couleurfond = lireTag($args, "couleurfond", "string");
    $convertir = lireTag($args, "convertir", "string");
    $source = lireTag($args, "source", "string");

    $search = "";
    $res = "";
    $limit = "";

    if ($deb != "") $debut = $deb;

    if ($aleatoire) $order = "order by " . " RAND()";
    else $order = " order by classement";

    if ($source != '') {
        $search .= " and $source=" . intval($id);
    } else {
        if ($id != "") $search .= " and id=\"$id\"";
        if ($produit != "") $search .= " and produit=\"$produit\"";
        if ($rubrique != "") $search .= " and rubrique=\"$rubrique\"";
        if ($dossier != "") $search .= " and dossier=\"$dossier\"";
        if ($contenu != "") $search .= " and contenu=\"$contenu\"";
    }
    if ($exclusion != "") $search .= " and id not in($exclusion)";

    $image = new Image();

    if ($debut != "") $debut--;
    else $debut = 0;

    $query = "select * from $image->table where 1 $search";

    if ($debut != "" && $num == "") $num = CacheBase::getCache()->query_count($query);


    if ($debut != "" || $num != "") $limit .= " limit $debut,$num";

    if ($nb != "") {
        $nb--;
        $limit .= " limit $nb,1";
    }

    $query = "select * from $image->table where 1 $search $order $limit";


    $pr = new Produit();
    $prdesc = new Produitdesc();
    $rudesc = new Rubriquedesc();
    $contenudesc = new Contenudesc();
    $dossierdesc = new Dossierdesc();

    $compt = 1;
    $result = CacheBase::getCache()->query($query);

    if ($result == "" || count($result) == 0) return "";

    foreach ($result as $row) {

        $image = new Image();
        $image->charger($row->id);
        $imagedesc = new Imagedesc();
        $imagedesc->charger($image->id);

        $temp = $texte;

        // Compatibilité
        $temp = str_replace("#FGRANDE", "#FICHIER", $temp);
        $temp = str_replace("#FPETITE", "#FICHIER", $temp);
        $temp = str_replace("#GRANDE", "#IMAGE", $temp);
        $temp = str_replace("#PETITE", "#IMAGE", $temp);

        if ($image->produit != 0) {
            $type = "produit";

            $pr->charger_id($image->produit);
            $prdesc->charger($image->produit);
            $temp = str_replace("#PRODTITRE", $prdesc->titre, $temp);
            $temp = str_replace("#PRODUIT", $image->produit, $temp);
            $temp = str_replace("#PRODREF", $pr->ref, $temp);
            $temp = str_replace("#RUBRIQUE", $pr->rubrique, $temp);
        } else if ($image->rubrique != 0) {

            $type = "rubrique";

            $rudesc->charger($image->rubrique);
            $temp = str_replace("#RUBRIQUE", $image->rubrique, $temp);
            $temp = str_replace("#RUBTITRE", $rudesc->titre, $temp);
        } else if ($image->dossier != 0) {

            $type = "dossier";

            $dosdesc = new Dossierdesc();
            $dosdesc->charger($image->dossier);
            $temp = str_replace("#DOSSIER", $image->dossier, $temp);
            $temp = str_replace("#DOSTITRE", $dosdesc->titre, $temp);
        } else if ($image->contenu != 0) {

            $type = "contenu";

            $ctdesc = new Contenudesc();
            $ctdesc->charger($image->contenu);
            $temp = str_replace("#CONTTITRE", $ctdesc->titre, $temp);
            $temp = str_replace("#CONTENU", $image->contenu, $temp);
        }


        if ($type != "") {

            if (!$largeur && !$hauteur)	{
              	$temp = str_replace("#IMAGE", FICHIER_URL."client/gfx/photos/$type/" . $image->fichier, $temp);
              	$mesdimensions=getimagesize(realpath(SITE_DIR) . "/client/gfx/photos/$type/" . $image->fichier);
  			} else {
              	$nomcache = redim($type, $image->fichier, $largeur, $hauteur, $opacite, $noiretblanc, $miroir, 1, $exact, $couleurfond, $convertir);
    			$temp = str_replace("#IMAGE", $nomcache, $temp);
              	$mesdimensions=getimagesize(realpath(SITE_DIR) . "/../..".$nomcache);
            }

            $temp = str_replace("#FICHIER", FICHIER_URL."client/gfx/photos/$type/" . $image->fichier, $temp);
        }

        $temp = str_replace("#ID", $image->id, $temp);
        $temp = str_replace("#TITRE", $imagedesc->titre, $temp);
        $temp = str_replace("#CHAPO", $imagedesc->chapo, $temp);
        $temp = str_replace("#DESCRIPTION", $imagedesc->description, $temp);
        $temp = str_replace("#COMPT", "$compt", $temp);
        $temp = str_replace("#NOMCACHE", "$nomcache", $temp);
        $temp = str_replace("#CACHE", "$nomcache", $temp);
        $temp = str_replace("#LARGEUR",  $mesdimensions[0], $temp);
        $temp = str_replace("#HAUTEUR",  $mesdimensions[1], $temp);

        $compt++;

        $res .= $temp;
    }

    return $res;
}


/* Gestion des boucles de type Client*/
function boucleClient($texte, $args) {
    // récupération des arguments
    $id = lireTag($args, "id", "int");
    $ref = lireTag($args, "ref", "string");
    $raison = lireTag($args, "raison", "int");
    $nom = lireTag($args, "nom", "string+\-\'\,\s\/\(\)\&\"");
    $prenom = lireTag($args, "prenom", "string+\-\'\,\s\/\(\)\&\"");
    $cpostal = lireTag($args, "cpostal", "int");
    $ville = lireTag($args, "ville", "string+\s\'\/\&\"");
    $email = lireTag($args, "email", "string+\@\.");
    $pays = lireTag($args, "pays", "int");
    $parrain = lireTag($args, "parrain", "int");
    $revendeur = lireTag($args, "revendeur", "int");
    $telfixe = lireTag($args, "telfixe", "string+\s\.\/");
    $telport = lireTag($args, "telport", "string+\s\.\/");


    $search = "";
    $res = "";

    // preparation de la requete
    if ($id != "") $search .= " and id=\"$id\"";
    if ($ref != "") $search .= " and ref=\"$ref\"";
    if ($raison != "") $search .= " and raison=\"$raison\"";
    if ($prenom != "") $search .= " and prenom=\"$prenom\"";
    if ($nom != "") $search .= " and nom=\"$nom\"";
    if ($cpostal != "") $search .= " and cpostal=\"$cpostal\"";
    if ($ville != "") $search .= " and ville=\"$ville\"";
    if ($email != "") $search .= " and email=\"$email\"";
    if ($pays != "") $search .= " and pays=\"$pays\"";
    if ($parrain != "") $search .= " and parrain=\"$parrain\"";
    if ($revendeur != "") $search .= " and type=\"$revendeur\"";
    if ($telfixe != "") $search .= " and telfixe=\"$telfixe\"";
    if ($telport != "") $search .= " and telport=\"$telport\"";

    $client = new Client();
    $order = "order by nom";

    $query = "select * from $client->table where 1 $search $order";

    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    foreach ($resul as $row) {

        $temp = str_replace("#ID", "$row->id", $texte);
        $temp = str_replace("#REF", "$row->ref", $temp);
        $temp = str_replace("#RAISON", "$row->raison", $temp);
        $temp = str_replace("#ENTREPRISE", "$row->entreprise", $temp);
        $temp = str_replace("#SIRET", "$row->siret", $temp);
        $temp = str_replace("#INTRACOM", "$row->intracom", $temp);
        $temp = str_replace("#NOM", "$row->nom", $temp);
        $temp = str_replace("#PRENOM", "$row->prenom", $temp);
        $temp = str_replace("#TELFIXE", "$row->telfixe", $temp);
        $temp = str_replace("#TELPORT", "$row->telport", $temp);
        $temp = str_replace("#EMAIL", "$row->email", $temp);
        $temp = str_replace("#ADRESSE1", "$row->adresse1", $temp);
        $temp = str_replace("#ADRESSE2", "$row->adresse2", $temp);
        $temp = str_replace("#ADRESSE3", "$row->adresse3", $temp);
        $temp = str_replace("#CPOSTAL", "$row->cpostal", $temp);
        $temp = str_replace("#VILLE", "$row->ville", $temp);
        $temp = str_replace("#PAYS", "$row->pays", $temp);
        $temp = str_replace("#PARRAIN", "$row->parrain", $temp);
        $temp = str_replace("#TYPE", "$row->type", $temp);
        $temp = str_replace("#POURCENTAGE", "$row->pourcentage", $temp);


        $res .= $temp;

    }


    return $res;

}

function boucleDevise($texte, $args) {

    // récupération des arguments
    $produit = lireTag($args, "produit", "int");
    $id = lireTag($args, "id", "int_list");
    $somme = lireTag($args, "somme", "float");
    $exclusion = lireTag($args, "exclusion", "int_list");

    $search = "";
    $limit = "";
    $res = "";

    if ($somme == "") $somme = 0;

    $prod = new Produit();
    if (!empty($produit)) $prod->charger_id($produit);

    if ($id != "") $search .= " and id in($id)";
    if ($exclusion != "") $search .= " and id not in($exclusion)";

    $url = preg_replace('/[\&\?]*devise=[0-9]+/', '', url_page_courante());
    $url .= strstr($url, '?') == false ? '?' : '&';

    $devise = new Devise();

    $query = "select * from $devise->table where 1 $search $limit";

    $resul = CacheBase::getCache()->query($query);

    //FIX : test d'existence de la session
    if (!isset($_SESSION["navig"])) $_SESSION["navig"] = new Navigation();
    if (!empty($resul)) {

        foreach ($resul as $row) {

            $devise->charger($row->id);

            $prix = $prix2 = $convert = 0;

            if (!empty($prod->id)) {
                $prix = $prod->prix * $devise->taux;
                $prix2 = $prod->prix2 * $devise->taux;
            }

            if (!empty($somme)) $convert = $somme * $devise->taux;

            $total = $_SESSION['navig']->panier->total(1) * $devise->taux;

            $temp = str_replace("#ID", $devise->id, $texte);

            $temp = str_replace("#PRIX2", formatter_somme($prix2), $temp);
            $temp = str_replace("#PRIX", formatter_somme($prix), $temp);
            $temp = str_replace("#TOTAL", formatter_somme($total), $temp);
            $temp = str_replace("#CONVERT", formatter_somme($convert), $temp);

            $temp = str_replace("#NOM", $devise->nom, $temp);
            $temp = str_replace("#CODE", $devise->code, $temp);
            $temp = str_replace("#TAUX", $devise->taux, $temp);
            $temp = str_replace("#HTMLSYMBOLE", htmlentities($devise->symbole, ENT_COMPAT, 'UTF-8', false), $temp);
            $temp = str_replace("#SYMBOLE", $devise->symbole, $temp);

            $temp = str_replace("#DEFAUT", $devise->defaut, $temp);
            $temp = str_replace("#COURANTE", $devise->id == ActionsDevises::instance()->get_id_devise_courante() ? "1" : "0", $temp);

            $temp = str_replace('#URL', $url . 'devise=' . $devise->id, $temp);

            $res .= $temp;
        }
    }

    return $res;

}

function boucleDocument($texte, $args) {

    // récupération des arguments
    $id = lireTag($args, "id", "int");
    $produit = lireTag($args, "produit", "int");
    $rubrique = lireTag($args, "rubrique", "int");
    $nb = lireTag($args, "nb", "int");
    $debut = lireTag($args, "debut", "int");
    $deb = lireTag($args, "deb", "int");
    $num = lireTag($args, "num", "int");
    $dossier = lireTag($args, "dossier", "int");
    $contenu = lireTag($args, "contenu", "int");
    $exclusion = lireTag($args, "exclusion", "int_list");
    $aleatoire = lireTag($args, "aleatoire", "int");
    $classement = lireTag($args, "classement", "string");
    $source = lireTag($args, "source", "string");

    $search = "";
    $order = "";
    $limit = "";
    $res = "";

    if ($deb != "") $debut = $deb;

    if ($aleatoire) $order = "order by " . " RAND()";
    else $order = " order by classement";

    if ($source != '') {
        $search .= " and $source=" . intval($id);
    } else {
        if ($id != "") $search .= " and id=\"$id\"";
        if ($produit != "") $search .= " and produit=\"$produit\"";
        if ($rubrique != "") $search .= " and rubrique=\"$rubrique\"";
        if ($dossier != "") $search .= " and dossier=\"$dossier\"";
        if ($contenu != "") $search .= " and contenu=\"$contenu\"";
    }

    if ($exclusion != "") $search .= " and id not in($exclusion)";

    $document = new Document();
    $documentdesc = new Documentdesc();

    if ($debut != "") $debut--;
    else $debut = 0;

    $query = "select * from $document->table where 1 $search";
    $resul = $document->query($query);
    $nbres = $document->num_rows($resul);
    if ($debut != "" && $num == "") $num = $nbres;

    if ($num != "") $limit .= " limit $debut,$num";
    if ($nb != "") {
        $nb--;
        $limit .= " limit $nb,1";
    }

    $query = "select * from $document->table where 1 $search $order $limit";

    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    foreach ($resul as $row) {
        $document->charger($row->id);
        $documentdesc->charger($document->id);

        $ext = substr($document->fichier, -3);

        $temp = str_replace("#TITRE", "$documentdesc->titre", $texte);
        $temp = str_replace("#CHAPO", "$documentdesc->chapo", $temp);
        $temp = str_replace("#DESCRIPTION", "$documentdesc->description", $temp);
        $temp = str_replace("#FICHIER", FICHIER_URL."client/document/" . $document->fichier, $temp);
        $temp = str_replace("#EXTENSION", "$ext", $temp);

        $res .= $temp;
    }


    return $res;

}

function boucleAccessoire($texte, $args) {

    // récupération des arguments
    $produit = lireTag($args, "produit", "int");
    $deb = lireTag($args, "deb", "int");
    $num = lireTag($args, "num", "int");
    $aleatoire = lireTag($args, "aleatoire", "int");
    $classement = lireTag($args, "classement", "string");
    $unique = lireTag($args, "unique", "int");

    $search = "";
    $order = "";
    $limit = "";
    $res = "";

    if (!$deb) $deb = 0;
    if (!$num) $num = "999999999";

    if ($produit) $search .= " and produit=\"$produit\"";
    $limit .= " limit $deb,$num";

    if ($classement == "manuel") $order = "order by classement";
    else if ($aleatoire) $order = "order by " . " RAND()";


    $accessoire = new Accessoire();

    if ($unique == "")
        $query = "select * from $accessoire->table where 1 $search $order $limit";
    else
        $query = "select DISTINCT(id) from $accessoire->table where 1 $search $order $limit";

    $compt = 1;
    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    foreach ($resul as $row) {

        $prod = new Produit();
        $prod->charger_id($row->produit);

        $temp = str_replace("#ACCESSOIRE", "$row->accessoire", $texte);
        $temp = str_replace("#PRODID", "$row->produit", $temp);
        $temp = str_replace("#PRODREF", $prod->ref, $temp);
        $temp = str_replace("#COMPT", "$compt", $temp);

        $compt++;

        $res .= $temp;
    }

    return $res;

}

function boucleProduit($texte, $args, $type = 0) {
    global $page, $totbloc, $ref, $id_rubrique;

    // récupération des arguments
    $rubrique = lireTag($args, "rubrique", "int_list");
    $rubcourante = lireTag($args, "rubcourante", "int");
    $deb = lireTag($args, "deb", "int");
    $num = lireTag($args, "num", "int");
    $passage = lireTag($args, "passage", "int");
    $ligne = lireTag($args, "ligne", "int+-");
    $bloc = lireTag($args, "bloc", "int+-");
    $nouveaute = lireTag($args, "nouveaute", "int");
    $promo = lireTag($args, "promo", "int");
    $reappro = lireTag($args, "reappro", "int");
    $refp = lireTag($args, "ref", "string");
    $id = lireTag($args, "id", "int_list");
    $garantie = lireTag($args, "garantie", "int");
    $motcle = lireTag($args, "motcle", "string+\s\'");
    $classement = lireTag($args, "classement", "string");
    $aleatoire = lireTag($args, "aleatoire", "int");
    $prixmin = lireTag($args, "prixmin", "float");
    $prixmax = lireTag($args, "prixmax", "float");
    $caracteristique = lireTag($args, "caracteristique", "int+-");
    $caracdisp = lireTag($args, "caracdisp", "int+-\*");
    $caracval = lireTag($args, "caracval", "string+\s\'\/\*");
    $typech = lireTag($args, "typech", "string");
    $declinaison = lireTag($args, "declinaison", "int+-");
    $declidisp = lireTag($args, "declidisp", "int+-");
    $declistockmini = lireTag($args, "declistockmini", "int");
    $stockmini = lireTag($args, "stockmini", "int");
    $courant = lireTag($args, "courant", "int");
    $profondeur = lireTag($args, "profondeur", "int");
    $exclusion = lireTag($args, "exclusion", "int_list");
    $exclurub = lireTag($args, "exclurub", "int_list");
    $poids = lireTag($args, "poids", "float");
    $stockvide = lireTag($args, "stockvide", "int");
    $forcepage = lireTag($args, "forcepage", "int");

    if ($bloc) $totbloc = $bloc;

    if (!$deb) $deb = 0;

    if ($page) $_SESSION['navig']->page = $page;
    if (!$page || $page == 1) $page = 0;

    if (!$totbloc) $totbloc = 1;
    if ($page) $deb = ($page - 1) * $totbloc * $num + $deb;

    if ($forcepage != "") {
        if ($forcepage == 1) {
            $forcepage = 0;
            $deb = 0;
        }

        if ($forcepage) $deb = ($forcepage - 1) * $totbloc * $num + $deb;
    }

    $produit = new Produit();

    // initialisation de variables
    $search = "";
    $order = "";
    $comptbloc = 0;
    $limit = "";
    $pourcentage = "";
    $res = "";
    $virg = "";

    if ($rubcourante == "1" && $rubrique != $id_rubrique)
        return "";

    // preparation de la requete

    if ($courant == "1") $search .= " and ref=\"$ref\"";
    else if ($courant == "0") $search .= " and ref<>\"$ref\"";

    if ($exclusion != "") $search .= " and $produit->table.id not in($exclusion)";
    if ($exclurub != "") $search .= " and rubrique not in($exclurub)";

    if ($rubrique != "") {
        $srub = "";

        if ($profondeur == "") $profondeur = 0;
        $tabrub = explode(",", $rubrique);
        for ($compt = 0; $compt < count($tabrub); $compt++) {
            $rec = arbreBoucle($tabrub[$compt], $profondeur);
            $rec = rtrim($rec, ",");
            if ($rec) $virg = ",";
            $srub .= $tabrub[$compt] . $virg . $rec . ',';
        }
        $srub = rtrim($srub, ",");

        $search .= " and rubrique in($srub)";
    }

    if ($ligne == "") $ligne = "1";

    if ($ligne != "-1") $search .= " and ligne=\"$ligne\"";
    if ($id != "") $search .= " and $produit->table.id in ($id)";
    if ($nouveaute != "") $search .= " and nouveaute=\"$nouveaute\"";
    if ($promo != "") $search .= " and promo=\"$promo\"";
    if ($reappro != "") $search .= " and reappro=\"$reappro\"";
    if ($garantie != "") $search .= " and garantie=\"$garantie\"";
    if ($prixmin != "") $search .= " and ((prix2>=\"$prixmin\" and promo=\"1\") or (prix>=\"$prixmin\" and promo=\"0\"))";
    if ($prixmax != "") $search .= " and ((prix2<=\"$prixmax\" and promo=\"1\") or (prix<=\"$prixmax\" and promo=\"0\"))";
    if ($poids != "") $search .= " and poids<=\"$poids\"";
    if ($stockmini != "" && $declistockmini == "") $search .= " and stock>=\"$stockmini\"";

    if ("" != $stockvide) {
        if (0 < $stockvide) {
            $search .= " and stock<=\"0\"";
        } elseif (0 >= $stockvide) {
            $search .= " and stock>\"0\"";
        }
    }

    if ($refp != "") $search .= " and ref=\"$refp\"";

    if ($bloc == "-1") $bloc = "999999999";
    if ($bloc != "" && $num != "") $limit .= " limit $deb,$bloc";
    else if ($num != "") $limit .= " limit $deb,$num";

    if ($aleatoire)
        $order = "order by " . " RAND()";
    else {
        $listeClassement = explode(',', $classement);

        $choixClassement = array();
        for ($i = 0; $i < count($listeClassement); $i++) {
            if ($listeClassement[$i] == "prixmin") $choixClassement[] = "IF(promo, prix2, prix) ASC";
            else if ($listeClassement[$i] == "prixmax") $choixClassement[] = "IF(promo, prix2, prix) DESC";
            else if ($listeClassement[$i] == "rubrique") $choixClassement[] = "rubrique";
            else if ($listeClassement[$i] == "manuel") $choixClassement[] = "classement";
            else if ($listeClassement[$i] == "inverse") $choixClassement[] = "classement desc";
            else if ($listeClassement[$i] == "date") $choixClassement[] = "datemodif desc";
            else if ($listeClassement[$i] == "titre") $choixClassement[] = "titre";
            else if ($listeClassement[$i] == "titreinverse") $choixClassement[] = "titre desc";
            else if ($listeClassement[$i] == "ref") $choixClassement[] = "ref";
            else if ($listeClassement[$i] == "promo") $choixClassement[] = "promo desc";
            else if ($listeClassement[$i] == "nouveaute") $choixClassement[] = "nouveaute desc";
            else if ($listeClassement[$i] == "poids") $choixClassement[] = "poids";
        }

        if (empty($choixClassement))
            $order = "order by classement";
        else {
            $order = "order by " . implode(',', $choixClassement);
        }
    }


    /* Demande de caracteristiques */
    if ($caracdisp != "") {

        if (substr($caracteristique, -1) != "-") $caracteristique .= "-";
        if (substr($caracdisp, -1) != "-") $caracdisp .= "-";

        $lcaracteristique = explode("-", $caracteristique);
        $lcaracdisp = explode("-", $caracdisp);

        $i = 0;

        $tcaracval = new Caracval();

        while ($i < count($lcaracteristique) - 1) {
            $caracteristique = $lcaracteristique[$i];
            $caracdisp = $lcaracdisp[$i];
            if ($caracdisp == "*") $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and caracdisp<>''";
            else $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and caracdisp='$caracdisp'";

            $liste = "";
            $resul = CacheBase::getCache()->query($query);
            if (empty($resul)) return;

            foreach ($resul as $row)
                $liste .= "'$row->produit',";

            $liste = rtrim($liste, ",");
            $i++;

            if ($liste != "") $search .= " and $produit->table.id in($liste)";
            else return "";
        }


    }

    if ($caracval != "") {

        $i = 0;
        $liste = "";

        $tcaracval = new Caracval();

        if ($caracval == "*") $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and valeur<>''";
        else if ($caracval == "-") $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and valeur=''";
        else if ($typech == "like") $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and valeur like '$caracval'";
        else $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and valeur ='$caracval'";

        $liste = "";
        $resul = CacheBase::getCache()->query($query);
        if ($resul == "" || count($resul) == 0) return "";
        foreach ($resul as $row)
            $liste .= "'$row->produit',";

        $liste = rtrim($liste, ",");

        $i++;


        if ($liste != "") $search .= " and $produit->table.id in($liste)";
        else return "";
    }


    /* Demande de declinaisons */
    if ($declidisp != "") {

        if (!strstr($declinaison, "-")) $declinaison .= "-";
        if (!strstr($declidisp, "-")) $declidisp .= "-";
        if (!strstr($declistockmini, "-")) $declistockmini .= "-";

        $ldeclinaison = explode("-", $declinaison);
        $ldeclidisp = explode("-", $declidisp);
        $ldeclistockmini = explode("-", $declistockmini);

        $i = 0;
        $liste = "";
        $exdecprod = new Exdecprod();
        $stock = new Stock();

        while ($i < count($ldeclinaison) - 1) {

            $declinaison = $ldeclinaison[$i];
            $declidisp = $ldeclidisp[$i];
            $declistockmini = $ldeclistockmini[$i];

            $query = "select * from $exdecprod->table where declidisp='$declidisp'";
            $resul = CacheBase::getCache()->query($query);
            if (count($resul) > 0)
                foreach ($resul as $row)
                    $liste .= "'$row->produit',";

            if ($liste != "") {
                $liste = rtrim($liste, ",");
                $search .= " and $produit->table.id not in($liste)";
            }

            $liste = "";

            if ($declistockmini != "") {
                $query = "select * from $stock->table where declidisp='$declidisp' and valeur>='$declistockmini'";
                $resul = CacheBase::getCache()->query($query);

                if (count($resul) > 0)
                    foreach ($resul as $row)
                        $liste .= "'$row->produit',";

                if ($liste != "") {
                    $liste = rtrim($liste, ",");
                    $search .= " and $produit->table.id in($liste)";
                } else return "";
            }

            $i++;

        }

    }

    $produit = new Produit();
    $produitdesc = new Produitdesc();


    if ($motcle) {
        $motcle = $produit->escape_string(strip_tags(trim($motcle)));
        $liste = "";

        $query = "
				SELECT pd.produit FROM
					$produitdesc->table pd
				LEFT JOIN
					$produit->table p ON p.id=pd.produit
				WHERE
                    pd.lang = " . ActionsLang::instance()->get_id_langue_courante() . "
                AND
					p.ref='$motcle'
				OR (
					match(pd.titre, pd.chapo, pd.description, pd.postscriptum) AGAINST ('$motcle' IN BOOLEAN MODE)
				OR
					pd.titre REGEXP '[[:<:]]${motcle}[[:>:]]'
				OR
				  	pd.chapo REGEXP '[[:<:]]${motcle}[[:>:]]'
				OR
				 	pd.description REGEXP '[[:<:]]${motcle}[[:>:]]'
				OR
				 	pd.postscriptum REGEXP '[[:<:]]${motcle}[[:>:]]'
    			)
			";

        $resul = CacheBase::getCache()->query($query);

        if (empty($resul)) return "";

        foreach ($resul as $row) {
            $liste .= "'$row->produit',";
        }

        $liste = rtrim($liste, ',');
        $search .= "and $produit->table.id in ($liste)";

    }

    if ($classement != "titre" && $classement != "titreinverse") {
        $query = "select * from $produit->table where 1 $search $order";
    } else {
        if (ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE) {
            // On retourne uniquement les produit traduites
            $search .= " and $produitdesc->table.id is not null";
        }

        $query = "
				select $produit->table.* from $produit->table
				left join $produitdesc->table on $produitdesc->table.produit = $produit->table.id and lang=" . ActionsLang::instance()->get_id_langue_courante() . "
				where  1 $search $order
			";
    }

    $nbres = count(CacheBase::getCache()->query($query));

    $query .= " $limit";

    $resul = CacheBase::getCache()->query($query);

    if (empty($resul)) return "";

    // substitutions
    if ($type) return $query;

    $count_query = "
			select
				count($produit->table.id) as totcount
			from
				$produit->table
			left join
				$produitdesc->table on $produitdesc->table.produit = $produit->table.id and $produitdesc->table.lang=" . ActionsLang::instance()->get_id_langue_courante() . "
			where
				1 $search
		";

    $countres = CacheBase::getCache()->query($count_query);
    $nbproduits = $countres ? $countres[0]->totcount : 0;

    $compt = 0;

    foreach ($resul as $row) {

        $compt++;

        if ($passage != "" && $comptbloc > $passage - 1)
            break;

        if ($num > 0)
            if ($comptbloc >= ceil($nbproduits / $num) && $bloc != "") continue;

        if ($comptbloc == 0) $debcourant = 0;
        else $debcourant = (int)$num * ($comptbloc);
        $comptbloc++;

        $prodid = $row->id;

        $rubriquedesc = new Rubriquedesc();
        $rubriquedesc->charger($row->rubrique);

        $produitdesc = new Produitdesc();
        $produitdesc->charger($prodid);

        $temp = $texte;

        if ($row->promo == "1") $temp = preg_replace("/\#PROMO\[([^]]*)\]\[([^]]*)\]/", "\\1", $temp);
        else $temp = preg_replace("/\#PROMO\[([^]]*)\]\[([^]]*)\]/", "\\2", $temp);

        if ($row->nouveaute == "1") $temp = preg_replace("/\#NOUVEAU\[([^]]*)\]\[([^]]*)\]/", "\\1", $temp);
        else $temp = preg_replace("/\#NOUVEAU\[([^]]*)\]\[([^]]*)\]/", "\\2", $temp);

        if ($row->promo == '1' && $row->prix) {
            $pourcentage = round((100 * ($row->prix - $row->prix2) / $row->prix), 0);
        } else {
            $pourcentage = null;
        }

        $prixorig = $row->prix;
        $prix2orig = $row->prix2;

        $prix = $row->prix - ($row->prix * $_SESSION['navig']->client->pourcentage / 100);
        $prix2 = $row->prix2 - ($row->prix2 * $_SESSION['navig']->client->pourcentage / 100);

        $ecotaxe = $row->ecotaxe;

        $pays = new Pays();
        $pays->charger($_SESSION['navig']->client->pays);

        $zone = new Zone();
        $zone->charger($pays->zone);

        $prixht = $prix / (1 + $row->tva / 100);
        $prix2ht = $prix2 / (1 + $row->tva / 100);
        $prixoright = $prixorig / (1 + $row->tva / 100);
        $prix2oright = $prix2orig / (1 + $row->tva / 100);

        $ecotaxeht = $row->ecotaxe / (1 + $row->tva / 100);

        $prix = formatter_somme($prix);
        $prix2 = formatter_somme($prix2);
        $prixht = formatter_somme($prixht);
        $prix2ht = formatter_somme($prix2ht);
        $prixorig = formatter_somme($prixorig);
        $prix2orig = formatter_somme($prix2orig);
        $prixoright = formatter_somme($prixoright);
        $prix2oright = formatter_somme($prix2oright);

        if ($deb != "" && !$page) $debcourant += $deb - 1;

        $temp = str_replace("#NBRES_TOTAL", $nbres, $temp);
        $temp = str_replace("#NBRES", $nbproduits, $temp);
        $temp = str_replace("#REF", "$row->ref", $temp);
        $temp = str_replace("#COMPT", "$compt", $temp);
        $temp = str_replace("#DATE", substr($row->datemodif, 0, 10), $temp);
        $temp = str_replace("#HEURE", substr($row->datemodif, 11), $temp);
        $temp = str_replace("#DEBCOURANT", "$debcourant", $temp);
        $temp = str_replace("#ID", "$prodid", $temp);
        $temp = str_replace("#PRIX2ORIGHT", "$prix2oright", $temp);
        $temp = str_replace("#PRIX2ORIG", "$prix2orig", $temp);
        $temp = str_replace("#PRIXORIGHT", "$prixoright", $temp);
        $temp = str_replace("#PRIXORIG", "$prixorig", $temp);
        $temp = str_replace("#PRIX2HT", "$prix2ht", $temp);
        $temp = str_replace("#PRIX2", "$prix2", $temp);
        $temp = str_replace("#PRIXHT", "$prixht", $temp);
        $temp = str_replace("#PRIX", "$prix", $temp);
        $temp = str_replace("#PROMO", "$row->promo", $temp);
        $temp = str_replace("#TVA", "$row->tva", $temp);
        $temp = str_replace("#ECOTAXEHT", "$ecotaxeht", $temp);
        $temp = str_replace("#ECOTAXE", "$row->ecotaxe", $temp);
        $temp = str_replace("#STOCK", "$row->stock", $temp);
        $temp = str_replace("#POURCENTAGE", "$pourcentage", $temp);
        $temp = str_replace("#RUBRIQUE", "$row->rubrique", $temp);
        $temp = str_replace("#PERSO", "$row->perso", $temp);
        $temp = str_replace("#POIDS", "$row->poids", $temp);
        $temp = str_replace("#TITRE", "$produitdesc->titre", $temp);
        $temp = str_replace("#STRIPTITRE", strip_tags($produitdesc->titre), $temp);
        $temp = str_replace("#CHAPO", "$produitdesc->chapo", $temp);
        $temp = str_replace("#STRIPCHAPO", strip_tags($produitdesc->chapo), $temp);
        $temp = str_replace("#DESCRIPTION", str_replace("../", "", $produitdesc->description), $temp);
        $temp = str_replace("#POSTSCRIPTUM", "$produitdesc->postscriptum", $temp);
        $temp = str_replace("#STRIPDESCRIPTION", strip_tags($produitdesc->description), $temp);
        $temp = str_replace("#URL", $produitdesc->getUrl(), $temp);
        $temp = str_replace("#REWRITEURL", $produitdesc->getUrl(), $temp);
        $temp = str_replace("#GARANTIE", "$row->garantie", $temp);
        $temp = str_replace("#PANIERAPPEND", urlfond("panier", "action=" . "ajouter&amp;ref=" . "$row->ref" . "&amp;" . "append=1", true), $temp);
        $temp = str_replace("#PANIER", urlfond("panier", "action=" . "ajouter" . "&amp;" . "ref=" . "$row->ref", true), $temp);
        $temp = str_replace("#RUBTITRE", "$rubriquedesc->titre", $temp);


        $res .= $temp;

    }


    return $res;

}


function boucleContenu($texte, $args, $type = 0) {
    global $page, $totbloc, $id_contenu;

    // récupération des arguments
    $dossier = lireTag($args, "dossier", "int");
    $ligne = lireTag($args, "ligne", "int");
    $deb = lireTag($args, "deb", "int");
    $num = lireTag($args, "num", "int");
    $bloc = lireTag($args, "bloc", "int");
    $id = lireTag($args, "id", "int_list");
    $motcle = lireTag($args, "motcle", "string+\s\'");
    $classement = lireTag($args, "classement", "string");
    $aleatoire = lireTag($args, "aleatoire", "int");
    $produit = lireTag($args, "produit", "int");
    $rubrique = lireTag($args, "rubrique", "int");
    $profondeur = lireTag($args, "profondeur", "int");
    $courant = lireTag($args, "courant", "int");
    $exclusion = lireTag($args, "exclusion", "int_list");
    $forcepage = lireTag($args, "forcepage", "int");

    if ($bloc) $totbloc = $bloc;
    if (!$deb) $deb = 0;

    if ($page) $_SESSION['navig']->page = $page;
    if (!$page || $page == 1) $page = 0;

    if (!$totbloc) $totbloc = 1;
    if ($page) $deb = ($page - 1) * $totbloc * $num + $deb;

    if ($forcepage != "") {
        if ($forcepage == 1) {
            $forcepage = 0;
            $deb = 0;
        }

        if ($forcepage) $deb = ($forcepage - 1) * $totbloc * $num + $deb;
    }

    // initialisation de variables
    $search = "";
    $order = "";
    $comptbloc = 0;
    $virg = "";
    $limit = "";
    $res = "";

    // preparation de la requete
    if ($dossier != "") {
        if ($profondeur == "") $profondeur = 0;
        $rec = arbreBoucle_dos($dossier, $profondeur);
        $rec = rtrim($rec, ",");
        if ($rec) $virg = ",";

        $search .= " and dossier in('$dossier'$virg$rec)";
    }

    if ($ligne == "") $ligne = "1";

    $search .= " and ligne=\"$ligne\"";

    if ($id != "") $search .= " and id in($id)";
    if ($courant == "1") $search .= " and id='$id_contenu'";
    else if ($courant == "0") $search .= " and id!='$id_contenu'";
    if ($exclusion != "") $search .= " and id not in($exclusion)";

    if ($bloc == "-1") $bloc = "999999999";
    if ($bloc != "" && $num != "") $limit .= " limit $deb,$bloc";
    else if ($num != "") $limit .= " limit $deb,$num";

    $liste = "";

    if ($rubrique != "" || $produit != "") {
        if ($rubrique) {
            $type_obj = 0;
            $objet = $rubrique;
        }
        else {
            $type_obj = 1;
            $objet = $produit;
        }

        $contenuassoc = new Contenuassoc();
        $query = "select * from $contenuassoc->table where objet=\"" . $objet . "\" and type=\"" . $type_obj . "\"";
        $resul = CacheBase::getCache()->query($query);

        if (empty($resul)) return "";

        foreach ($resul as $row)
            $liste .= "'" . $row->contenu . "',";

        $liste = rtrim($liste, ",");

        if ($liste != "") $search .= " and id in ($liste)";
        else $search .= " and id in ('')";

        $type_obj = "";
    }


    if ($aleatoire) $order = "order by " . " RAND()";
    else if ($classement == "manuel") $order = "order by classement";
    else if ($classement == "inverse") $order = "order by classement desc";
    else if ($classement == "date") $order = "order by datemodif desc";

    $contenu = new Contenu();
    $contenudesc = new Contenudesc();

    if ($motcle) {
        $motcle = $contenu->escape_string(strip_tags(trim($motcle)));
        $liste = "";

        $query = "
				SELECT cd.contenu FROM
					$contenudesc->table cd
				WHERE
                    cd.lang = " . ActionsLang::instance()->get_id_langue_courante() . "
                AND (
					match(cd.titre, cd.chapo, cd.description, cd.postscriptum) AGAINST ('$motcle' IN BOOLEAN MODE)
				OR
					cd.titre REGEXP '[[:<:]]${motcle}[[:>:]]'
				OR
				  	cd.chapo REGEXP '[[:<:]]${motcle}[[:>:]]'
				OR
				 	cd.description REGEXP '[[:<:]]${motcle}[[:>:]]'
				OR
				 	cd.postscriptum REGEXP '[[:<:]]${motcle}[[:>:]]'
    			)
			  ";

        $resul = CacheBase::getCache()->query($query);
        if (empty($resul)) return "";

        foreach ($resul as $row) {
            $liste .= "'$row->contenu',";
        }

        $liste = rtrim($liste, ',');
        $query = "select * from $contenu->table where id in ($liste) and ligne=\"$ligne\" $limit";
        $saveReq = "select * from $contenu->table where id in ($liste) and ligne=\"$ligne\"";
    }
    else $query = "select * from $contenu->table where 1 $search $order $limit";

    $saveReq = "select * from $contenu->table where 1 $search";

    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    // substitutions
    if ($type) return $query;

    $saveReq = str_replace("*", "count(*) as totcount", $saveReq);
    $saveRes = CacheBase::getCache()->query($saveReq);
    $countRes = $saveRes[0]->totcount;

    $compt = 1;

    foreach ($resul as $row) {
        if ($num > 0) if ($comptbloc >= ceil($countRes / $num) && $bloc != "") continue;

        if ($comptbloc == 0) $debcourant = 0;
        else $debcourant = (int)$num * ($comptbloc);
        $comptbloc++;

        $dossierdesc = new Dossierdesc();
        $dossierdesc->charger($row->dossier);
        $contenudesc = new Contenudesc();
        $contenudesc->charger($row->id);

        $temp = $texte;

        $temp = str_replace("#DATE", substr($row->datemodif, 0, 10), $temp);
        $temp = str_replace("#HEURE", substr($row->datemodif, 11), $temp);
        $temp = str_replace("#DEBCOURANT", "$debcourant", $temp);
        $temp = str_replace("#ID", "$row->id", $temp);
        $temp = str_replace("#DOSSIER", "$row->dossier", $temp);
        $temp = str_replace("#TITRE", "$contenudesc->titre", $temp);
        $temp = str_replace("#STRIPTITRE", strip_tags($contenudesc->titre), $temp);
        $temp = str_replace("#CHAPO", "$contenudesc->chapo", $temp);
        $temp = str_replace("#STRIPCHAPO", strip_tags($contenudesc->chapo), $temp);
        $temp = str_replace("#DESCRIPTION", str_replace("../", "", $contenudesc->description), $temp);
        $temp = str_replace("#POSTSCRIPTUM", "$contenudesc->postscriptum", $temp);
        $temp = str_replace("#STRIPDESCRIPTION", strip_tags($contenudesc->description), $temp);
        $temp = str_replace("#URL", $contenudesc->getUrl(), $temp);
        $temp = str_replace("#REWRITEURL", $contenudesc->getUrl(), $temp);
        $temp = str_replace("#DOSTITRE", "$dossierdesc->titre", $temp);
        $temp = str_replace("#PRODUIT", "$produit", $temp);
        $temp = str_replace("#RUBRIQUE", "$rubrique", $temp);
        $temp = str_replace("#COMPT", "$compt", $temp);

        $res .= $temp;

        $compt++;
    }

    return $res;
}


function boucleContenuassoc($texte, $args) {
    $objet = lireTag($args, "objet", "int");
    $typeobj = lireTag($args, "typeobj", "int");
    $contenu = lireTag($args, "contenu", "int");
    $classement = lireTag($args, "classement", "string");
    $num = lireTag($args, "num", "int");
    $deb = lireTag($args, "deb", "int");

    if (!$deb) $deb = 0;

    $search = "";

    if ($objet != "")
        $search .= " and objet=\"$objet\"";

    if ($typeobj != "")
        $search .= " and type=\"$typeobj\"";

    if ($contenu != "")
        $search .= " and contenu=\"$contenu\"";

    $order = "";
    $limit = "";

    if ($num != "") $limit .= " limit $deb,$num";

    if ($classement == "manuel")
        $order = "order by classement";

    $contenuassoc = new Contenuassoc();
    $query = "select * from $contenuassoc->table where 1 $search $order $limit";

    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    $compt = 1;
    $res = '';
    foreach ($resul as $row) {
        $temp = str_replace("#OBJET", $row->objet, $texte);
        $temp = str_replace("#TYPE", $row->type, $temp);
        $temp = str_replace("#CONTENU", $row->contenu, $temp);
        $temp = str_replace("#COMPTE", $compt, $temp);

        $compt++;
        $res .= $temp;

    }

    return $res;

}

function boucleLangue($texte, $args) {
    $exclure = lireTag($args, 'exclure');
    $id = lireTag($args, 'id');

    $res = '';

    $url = preg_replace('/[\&\?]*lang=[0-9]+/', '', url_page_courante());

    // S'il faut suffixer par lang=x, s'assurer de placer le bon séparateur
    if (!ActionsLang::instance()->get_un_domaine_par_langue()) {
        $url .= strstr($url, '?') == false ? '?' : '&';
    }

    $lng = new Lang();

    $query = 'select * from ' . $lng->table . ' where 1 ';

    if ($id != '') $query .= ' and id in ( ' . $id . ')';
    if ($exclure != '') $query .= ' and id not in ( ' . $exclure . ')';

    // Trouver l'url ré-écrite, si elle existe
    $reecriture = new Reecriture();

    if (Variable::lire("rewrite") != 0) {

        // L'URL de la page courante
        $requrl = lireParam('url', 'string');

        if ($requrl != '') $reecriture->charger($requrl);
    }

    $lngredir = new Reecriture();

    $result = $lng->query($query);

    while ($result && $row = $lng->fetch_object($result)) {
        $lng->charger($row->id);

        if ($reecriture->actif && $lngredir->charger_param($reecriture->fond, $reecriture->param, $lng->id, 1)) {

            if (ActionsLang::instance()->get_un_domaine_par_langue())
                $lngurl = "$row->url/$lngredir->url";
            else
                $lngurl = $lngredir->url;
        } else {
            if (ActionsLang::instance()->get_un_domaine_par_langue()) {
                $lngurl = str_replace(ActionsLang::instance()->get_langue_courante()->url, $row->url, $url);
            } else {
                $lngurl = $url . 'lang=' . $lng->id;
            }
        }

        $tmp = str_replace('#ID', $lng->id, $texte);
        $tmp = str_replace('#DESCRIPTION', $lng->description, $tmp);
        $tmp = str_replace('#CODE', $lng->code, $tmp);
        $tmp = str_replace('#DEFAUT', $lng->defaut ? '1' : '0', $tmp);
        $tmp = str_replace('#URL', $lngurl, $tmp);

        $res .= $tmp;
    }

    return $res;
}

function bouclePage($texte, $args) {
    global $page, $id_rubrique, $id_dossier;

    // récupération des arguments

    $num = lireTag($args, "num", "int");
    $courante = lireTag($args, "courante", "int");
    $pagecourante = lireTag($args, "pagecourante", "int");
    $typeaff = lireTag($args, "typeaff", "int");
    $max = lireTag($args, "max", "int");
    $affmin = lireTag($args, "affmin", "int");
    $avance = lireTag($args, "avance", "string");
    $type_page = lireTag($args, "type_page", "int");

    /** PARAMÈTRES DÉPRÉCIÉS, A NE PLUS UTILISER */
    $deb = lireTag($args, "deb", "int");
    $totbloc = lireTag($args, "totbloc", "int");
    /** FIN PARAMÈTRES DÉPRÉCIÉS */

    $i = "";

    if ($page <= 0) $page = 1;
    $bpage = $page;
    $res = "";

    $cnx = new Cnx();
    if (!$type_page)
        $query = boucleProduit($texte, str_replace("num", "null", $args), 1);
    else
        $query = boucleContenu($texte, str_replace("num", "null", $args), 1);

    if ($query != "") {
        $pos = strpos($query, "limit");
        if ($pos > 0) $query = substr($query, 0, $pos);

        $resul = $cnx->query($query);
        $nbres = $cnx->num_rows($resul);
    } else $nbres = 0;

    $page = $bpage;

    $nbpage = $num ? ceil($nbres / $num) : 0;
    if ($page + 1 > $nbpage) $pagesuiv = $page;
    else $pagesuiv = $page + 1;

    if ($page - 1 <= 0) $pageprec = 1;
    else $pageprec = $page - 1;


    if ($nbpage < $affmin) return;
    if ($nbpage == 1) return;

    if ($typeaff == 1) {
        if (!$max) $max = $nbpage + 1;
        if ($page && $max && $page > $max) $i = ceil(($page) / $max) * $max - $max + 1;

        if ($i == 0) $i = 1;

        $fin = $i + $max;


        for (; $i < $nbpage + 1 && $i < $fin; $i++) {

            $temp = str_replace("#PAGE_NUM", "$i", $texte);
            $temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
            $temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
            $temp = str_replace("#RUBRIQUE", "$id_rubrique", $temp);
            $temp = str_replace("#DOSSIER", "$id_dossier", $temp);

            if ($pagecourante && $pagecourante == $i) {

                if ($courante == "1" && $page == $i) $res .= $temp;
                else if ($courante == "0" && $page != $i) $res .= $temp;
                else if ($courante == "") $res .= $temp;
            } else if (!$pagecourante) $res .= $temp;
        }

    } else if ($typeaff == "0" && ($avance == "precedente" && $pageprec != $page)) {

        $temp = str_replace("#PAGE_NUM", "$page", $texte);
        $temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
        $temp = str_replace("#RUBRIQUE", "$id_rubrique", $temp);
        $temp = str_replace("#DOSSIER", "$id_dossier", $temp);
        $res .= $temp;
    } else if ($typeaff == "0" && ($avance == "suivante" && $pagesuiv != $page)) {
        $temp = str_replace("#PAGE_NUM", "$page", $texte);
        $temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
        $temp = str_replace("#RUBRIQUE", "$id_rubrique", $temp);
        $temp = str_replace("#DOSSIER", "$id_dossier", $temp);
        $res .= $temp;
    } else if ($typeaff == "0" && $avance == "") {

        $temp = str_replace("#PAGE_NUM", "$page", $texte);
        $temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
        $temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
        $temp = str_replace("#RUBRIQUE", "$id_rubrique", $temp);
        $temp = str_replace("#DOSSIER", "$id_dossier", $temp);
        $res .= $temp;
    } else if ($typeaff == "2") {
        /** DEPREACTED le typeaff 2 n'est plus utilisé, concervé pour rétrocompatibilité */
        if (!$deb) $deb = 0;

        if ($page) $_SESSION['navig']->page = $page;
        if (!$page || $page == 1) $page = 0;

        if (!$totbloc) $totbloc = 1;
        if ($page) $deb = ($page - 1) * $totbloc * $num + $deb;

        $fin = $deb + $num;
        $query = str_replace("*", "count(*) as max", $query);
        $resul = $cnx->query($query);

        $max = $cnx->get_result($resul, 0, "max");

        if ($fin > $max) $fin = $max;
        $temp = str_replace("#DEBUT", $deb, $texte);
        $temp = str_replace("#FIN", $fin, $temp);
        $temp = str_replace("#MAX", $max, $temp);

        $res .= $temp;
        /** END DEPRECATED */
    }


    return $res;


}


function bouclePanier($texte, $args) {

    $deb = lireTag($args, "deb", "int");
    $fin = lireTag($args, "fin", "int");
    $dernier = lireTag($args, "dernier", "int");
    $ref = lireTag($args, "ref", "string");
    $parent = lireTag($args, "parent", "int+-");

    if (!$deb) $deb = 0;
    if (!$fin) $fin = $_SESSION['navig']->panier->nbart;
    if ($dernier == 1)
        $deb = $_SESSION['navig']->panier->nbart - 1;

    $total = 0;
    $res = "";

    if (!$_SESSION['navig']->panier->nbart) return;

    for ($i = $deb; $i < $fin; $i++) {

        $article = & $_SESSION['navig']->panier->tabarticle[$i];

        if ($ref != "" && $article->produit->ref != $ref)
            continue;

        $ceParent = $article->parent;

        if ($ceParent === '')
            $ceParent = -1;

        if ($parent != "" && $ceParent != $parent)
            continue;

        $plus = $article->quantite + 1;
        $moins = $article->quantite - 1;

        if ($moins == 0) $moins++;

        $quantite = $article->quantite;
        $tva = $article->produit->tva;

        if (!$article->produit->promo)
            $prix = $article->produit->prix - ($article->produit->prix * $_SESSION['navig']->client->pourcentage / 100);
        else $prix = $article->produit->prix2 - ($article->produit->prix2 * $_SESSION['navig']->client->pourcentage / 100);

        $prixht = $prix / (1 + ($tva / 100));
        $totalht = $prixht * $quantite;

        $total = $prix * $quantite;

        $port = port();
        if ($port < 0)
            $port = 0;

        $totcmdport = $total + $port;

        $totsansport = $_SESSION['navig']->panier->total();

        $pays = new Pays();
        $pays->charger($_SESSION['navig']->client->pays);

        $zone = new Zone();
        $zone->charger($pays->zone);

        $portht = $port * 100 / (100 + $tva);
        $totcmdportht = $totcmdport * 100 / (100 + $tva);
        $totsansportht = $totsansport * 100 / (100 + $tva);

        $declidisp = new Declidisp();
        $declidispdesc = new Declidispdesc();
        $declinaison = new Declinaison();
        $declinaisondesc = new Declinaisondesc();

        $dectexte = "";
        $decval = "";


        if ($_SESSION['navig']->adresse) {
            $adr = new Adresse();
            $adr->charger($_SESSION['navig']->adresse);
            $idpays = $adr->pays;
        } else {
            $idpays = $_SESSION['navig']->client->pays;
        }

        $pays = new Pays();
        $pays->charger($idpays);

        $val = "";

        for ($compt = 0; $compt < count($article->perso); $compt++) {
            $tperso = $article->perso[$compt];
            $declinaison->charger($tperso->declinaison);
            // recup valeur declidisp ou string
            if ($declinaison->isDeclidisp($tperso->declinaison)) {
                $declidisp->charger($tperso->valeur);
                $declidispdesc->charger_declidisp($declidisp->id);
                $val = $declidispdesc->titre . " ";
            } else $val = $tperso->valeur . " ";

            // recup declinaison associee
            $declinaisondesc->charger($tperso->declinaison);
            $dectexte .= $declinaisondesc->titre . " " . $val . " ";
            $decval .= $val . " ";
        }

        if ($pays->tva != "" && (!$pays->tva)) {
            $prix = $prixht;
            $total = $totalht;
        }

        $temp = str_replace("#REF", $article->produit->ref, $texte);
        $temp = str_replace("#TITRE", $article->produitdesc->titre, $temp);
        $temp = str_replace("#QUANTITE", "$quantite", $temp);
        $temp = str_replace("#PRODUIT", $article->produitdesc->produit, $temp);
        $temp = str_replace("#PRIXUHT", formatter_somme($prixht), $temp);
        $temp = str_replace("#PRIXHT", formatter_somme($prixht), $temp);
        $temp = str_replace("#TOTALHT", formatter_somme($totalht), $temp);
        $temp = str_replace("#PRIXU", formatter_somme($prix), $temp);
        $temp = str_replace("#PRIX", formatter_somme($prix), $temp);
        $temp = str_replace("#TVA", "$tva", $temp);
        $temp = str_replace("#TOTAL", formatter_somme($total), $temp);
        $temp = str_replace("#ID", $article->produit->id, $temp);
        $temp = str_replace("#ARTICLE", "$i", $temp);
        $temp = str_replace("#PLUSURL", urlfond("panier", "action=" . "modifier" . "&amp;article=" . $i . "&amp;quantite=" . $plus, true), $temp);
        $temp = str_replace("#MOINSURL", urlfond("panier", "action=" . "modifier" . "&amp;article=" . $i . "&amp;quantite=" . $moins, true), $temp);
        $temp = str_replace("#SUPPRURL", urlfond("panier", "action=" . "supprimer" . "&amp;article=" . $i, true), $temp);
        //$temp = str_replace("#PRODURL", $article->produitdesc->getUrl(), $temp);
        $temp = str_replace("#TOTSANSPORTHT", formatter_somme($totsansportht), $temp);
        $temp = str_replace("#PORTHT", formatter_somme($portht), $temp);
        $temp = str_replace("#TOTPORTHT", formatter_somme($totcmdportht), $temp);
        $temp = str_replace("#TOTSANSPORT", formatter_somme($totsansport), $temp);
        $temp = str_replace("#PORT", formatter_somme($port), $temp);
        $temp = str_replace("#TOTPORT", formatter_somme($totcmdport), $temp);
        $temp = str_replace("#DECTEXTE", "$dectexte", $temp);
        $temp = str_replace("#DECVAL", "$decval", $temp);

        $res .= $temp;
    }

    return $res;

}


function boucleQuantite($texte, $args) {
    // récupération des arguments

    $res = "";

    $article = lireTag($args, "article", "int");
    $ref = lireTag($args, "ref", "string");
    $max = lireTag($args, "max", "int");
    $min = lireTag($args, "min", "int");
    $force = lireTag($args, "force", "int");
    $valeur = lireTag($args, "valeur", "int");


    $prodtemp = new Produit();
    if ($article != "") {
        $stockprod = 0;
        $prodtemp->charger($_SESSION['navig']->panier->tabarticle[$article]->produit->ref);
        $stockprod = $prodtemp->stock;

        for ($i = 0; $i < count($_SESSION['navig']->panier->tabarticle[$article]->perso); $i++) {
            $stock = new Stock();
            $stock->charger($_SESSION['navig']->panier->tabarticle[$article]->perso[$i]->valeur, $_SESSION['navig']->panier->tabarticle[$article]->produit->id);
            if ($stock->valeur < $stockprod)
                $stockprod = $stock->valeur;
        }

        if ($max != "" && $max > $stockprod)
            $max = $stockprod;
    } else if ($ref != "")
        $prodtemp->charger($ref);

    if ($min == "") $min = 1;

    if ($max == "")
        $max = $stockprod;

    if ($max == "" && $force == "")
        return;

    if ($stockprod != "" && $min > $stockprod && $force == "") return;

    $j = 0;

    if ($force != "" && $valeur != "") {
        $min = 1;
        $max = $valeur;
    }

    for ($i = $min; $i <= $max; $i++) {
        if ($i == $_SESSION['navig']->panier->tabarticle[$article]->quantite) $selected = "selected=\"selected\"";
        else $selected = "";

        $temp = str_replace("#NUM", "$i", $texte);
        $temp = str_replace("#SELECTED", $selected, $temp);
        $temp = str_replace("#REF", $ref, $temp);

        $res .= "$temp";
    }


    return $res;

}

function boucleChemin($texte, $args) {
    global $id_rubrique;

    // récupération des arguments

    $rubrique = lireTag($args, "rubrique", "int");
    $profondeur = lireTag($args, "profondeur", "int");
    $niveau = lireTag($args, "niveau", "int");

    if ($rubrique == "") return "";

    $res = "";

    $trubrique = new Rubrique();
    $trubrique->charger($rubrique);
    $trubriquedesc = new Rubriquedesc();

    $i = 0;

    if (!$trubrique->parent)
        return "";

    $rubtab = "";
    $tmp = new Rubrique();
    $tmp->charger($trubrique->parent);
    $rubtab[$i] = new Rubrique();
    $rubtab[$i++] = $tmp;

    while ($tmp->parent != 0) {
        $tmp = new Rubrique();
        $tmp->charger($rubtab[$i - 1]->parent);

        $rubtab[$i] = new Rubrique();
        $rubtab[$i++] = $tmp;
    }

    $compt = 0;

    for ($i = count($rubtab) - 1; $i >= 0; $i--) {
        if ($profondeur != "" && $compt == $profondeur) break;
        if ($niveau != "" && $niveau != $compt + 1) {
            $compt++;
            continue;
        }
        $trubriquedesc->charger($rubtab[$i]->id);
        $temp = str_replace("#ID", $rubtab[$i]->id, $texte);
        $temp = str_replace("#TITRE", "$trubriquedesc->titre", $temp);
        $temp = str_replace("#URL", $trubriquedesc->getUrl(), $temp);
        $temp = str_replace("#REWRITEURL", $trubriquedesc->getUrl(), $temp);

        $compt++;

        $res .= $temp;
    }

    return $res;

}

function boucleChemindos($texte, $args) {
    global $id_dossier;

    // récupération des arguments

    $dossier = lireTag($args, "dossier", "int");
    $profondeur = lireTag($args, "profondeur", "int");
    $niveau = lireTag($args, "niveau", "int");

    if ($dossier == "") return "";

    $res = "";

    $tdossier = new Dossier();
    $tdossier->charger($dossier);
    $tdossierdesc = new Dossierdesc();

    $i = 0;

    if (!$tdossier->parent) return "";

    $dostab = [];
    $tmp = new Dossier();
    $tmp->charger($tdossier->parent);
    $dostab[$i] = new Dossier();
    $dostab[$i++] = $tmp;

    while ($tmp->parent != 0) {
        $tmp = new Dossier();
        $tmp->charger($dostab[$i - 1]->parent);

        $dostab[$i] = new Dossier();
        $dostab[$i++] = $tmp;
    }

    $compt = 0;

    for ($i = count($dostab) - 1; $i >= 0; $i--) {
        if ($profondeur != "" && $compt == $profondeur) break;
        if ($niveau != "" && $niveau != $compt + 1) {
            $compt++;
            continue;
        }
        $tdossierdesc->charger($dostab[$i]->id);
        $temp = str_replace("#ID", $dostab[$i]->id, $texte);
        $temp = str_replace("#TITRE", "$tdossierdesc->titre", $temp);
        $temp = str_replace("#URL", $tdossierdesc->getUrl(), $temp);
        $temp = str_replace("#REWRITEURL", $tdossierdesc->getUrl(), $temp);

        $compt++;

        $res .= $temp;
    }

    return $res;

}

function bouclePaiement($texte, $args) {

    $res = "";

    $id = lireTag($args, 'id', 'int_list');
    $nom = lireTag($args, 'nom', 'string_list');
    $exclusion = lireTag($args, 'exclusion', 'string_list');

    $search = '';

    // preparation de la requete
    if ($id !== '') {
        $id = explode(',', $id);

        foreach ($id as &$anId) {
            $anId = (int)trim($anId);
        }
        unset($anId);

        if (count($id) === 1) {
            $search .= ' AND `id` = ' . $id[0];
        } else {
            $search .= ' AND `id` IN (' . implode(', ', $id) . ')';
        }
    }

    if ($nom !== '') {
        $nom = explode(',', $nom);

        foreach ($nom as &$aName) {
            $aName = '"' . trim($aName) . '"';
        }
        unset($aName);

        if (count($nom) === 1) {
            $search .= ' AND `nom` = ' . $nom[0];
        } else {
            $search .= ' AND `nom` IN (' . implode(', ', $nom) . ')';
        }
    }

    if ($exclusion !== '') {
        $exclusion = explode(',', $exclusion);

        foreach ($exclusion as &$aName) {
            $aName = '"' . trim($aName) . '"';
        }
        unset($aName);

        if (count($exclusion) === 1) {
            $search .= ' AND `nom` != ' . $exclusion[0];
        } else {
            $search .= ' AND `nom` NOT IN (' . implode(', ', $exclusion) . ')';
        }
    }


    $modules = new Modules();

    $query = "select * from $modules->table where type='1' and actif='1' $search order by classement";

    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    foreach ($resul as $row) {

        $modules = new Modules();
        $modules->charger_id($row->id);

        try {
            $instance = ActionsModules::instance()->instancier($modules->nom);

            $titre = $instance->getTitre();
            $chapo = $instance->getChapo();
            $description = $instance->getDescription();
        } catch (Exception $ex) {
            $titre = $$chapo = $description = '';
        }

        // Chercher le logo
        $exts = array('png', 'gif', 'jpeg', 'jpg');
        $logo = false;
        foreach ($exts as $ext) {
            $tmp = ActionsModules::instance()->lire_chemin_base() . "/$row->nom/logo.$ext";
            if (file_exists($tmp)) {
                $logo = ActionsModules::instance()->lire_url_base() . "/$row->nom/logo.$ext";
                break;
            }
        }

        $temp = str_replace("#ID", "$row->id", $texte);
        $temp = str_replace("#URLPAYER", urlfond("commande", "action=paiement&amp;type_paiement=" . $row->id, true), $temp);
        $temp = str_replace("#LOGO", $logo, $temp);
        $temp = str_replace("#TITRE", $titre, $temp);
        $temp = str_replace("#CHAPO", $chapo, $temp);
        $temp = str_replace("#DESCRIPTION", $description, $temp);
        $temp = str_replace("#NOM", $row->nom, $temp);
        $res .= $temp;
    }


    return $res;

}

function bouclePays($texte, $args) {


    $id = lireTag($args, "id", "int");
    $zone = lireTag($args, "zone", "int");
    $zdefinie = lireTag($args, "zdefinie", "int");
    $select = lireTag($args, "select", "int");
    $defaut = lireTag($args, "defaut", "int");
    $exclusion = lireTag($args, "exclusion", "int_list");


    $search = "";
    $res = "";

    $pays = new Pays();
    $paysdesc = new Paysdesc();

    // preparation de la requete
    if ($id != "") $search .= " and $pays->table.id=\"$id\"";
    if ($zone != "") $search .= " and $pays->table.zone=\"$zone\"";
    if ($zdefinie != "") $search .= " and $pays->table.zone<>\"-1\"";
    if ($defaut != "") $search .= " and $pays->table.defaut=\"1\"";
    if ($exclusion != "") $search .= " and $pays->table.id not in($exclusion)";

    if (ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE) {
        // On retourne uniquement les pays traduites
        $search .= " and $paysdesc->table.id is not null";
    }

    $query = "
					select $pays->table.id from $pays->table
					left join $paysdesc->table on $paysdesc->table.pays = $pays->table.id and $paysdesc->table.lang = " . ActionsLang::instance()->get_id_langue_courante() . "
					where 1 $search
					order by $paysdesc->table.titre
				";

    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    foreach ($resul as $row) {
        $paysdesc->charger($row->id);
        $pays->charger($row->id);

        $temp = str_replace("#ID", "$pays->id", $texte);
        $temp = str_replace("#TITRE", "$paysdesc->titre", $temp);
        $temp = str_replace("#CHAPO", "$paysdesc->chapo", $temp);
        $temp = str_replace("#DESCRIPTION", "$paysdesc->description", $temp);
        if (($_SESSION['navig']->formcli->pays == $pays->id || $_SESSION['navig']->client->pays == $pays->id) && $select == "")
            $temp = str_replace("#SELECTED", "selected=\"selected\"", $temp);
        if ($select != "" && $select == $pays->id) $temp = str_replace("#SELECTED", "selected=\"selected\"", $temp);
        else $temp = str_replace("#SELECTED", "", $temp);
        if ($pays->defaut == "1") $temp = str_replace("#DEFAUT", "selected=\"selected\"", $temp);
        else $temp = str_replace("#DEFAUT", "", $temp);

        $temp = str_replace("#TVA", $pays->tva, $temp);
        $temp = str_replace("#NUMEROISO", $pays->isocode, $temp);
        $temp = str_replace("#CODEISO2", $pays->isoalpha2, $temp);
        $temp = str_replace("#CODEISO3", $pays->isoalpha3, $temp);

        $res .= $temp;
    }

    return $res;
}

function boucleRaison($texte, $args) {

    $id = lireTag($args, "id", "int");
    $select = lireTag($args, "select", "int");
    $defaut = lireTag($args, "defaut", "int");
    $exclusion = lireTag($args, "exclusion", "int");

    $search = "";
    $res = "";

    // preparation de la requete
    if ($id != "") $search .= " and id=\"$id\"";
    if ($defaut != "") $search .= " and `defaut`=\"1\"";
    if ($exclusion != "") $search .= " and id not in($exclusion)";

    $raison = new Raison();
    $raisondesc = new Raisondesc();

    $query = "select * from $raison->table where 1 $search order by classement";
    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    foreach ($resul as $row) {
        $raisondesc = new Raisondesc();
        $raisondesc->charger($row->id);

        $temp = str_replace("#ID", "$row->id", $texte);
        $temp = str_replace("#COURT", "$raisondesc->court", $temp);
        $temp = str_replace("#LONG", "$raisondesc->long", $temp);
        if (($_SESSION['navig']->formcli->raison == $row->id || $_SESSION['navig']->client->raison == $row->id) && $select == "") {
            $temp = str_replace("#SELECTED", "selected=\"selected\"", $temp);
            $temp = str_replace("#CHECKED", "checked=\"checked\"", $temp);
        }
        if ($select != "" && $select == $row->id) {
            $temp = str_replace("#SELECTED", "selected=\"selected\"", $temp);
            $temp = str_replace("#CHECKED", "checked=\"checked\"", $temp);
        } else {
            $temp = str_replace("#SELECTED", "", $temp);
            $temp = str_replace("#CHECKED", "", $temp);
        }
        if ($row->defaut == "1")
            $temp = str_replace("#DEFAUT", "selected=\"selected\"", $temp);
        else
            $temp = str_replace("#DEFAUT", "", $temp);

        $res .= $temp;
    }

    return $res;
}

function boucleCaracteristique($texte, $args) {

    global $caracteristique;

    $id = lireTag($args, "id", "int_list");
    $rubrique = lireTag($args, "rubrique", "int");
    $affiche = lireTag($args, "affiche", "int");
    $produit = lireTag($args, "produit", "int");
    $courante = lireTag($args, "courante", "int");
    $exclusion = lireTag($args, "exclusion", "int_list");

    $search = "";
    $res = "";

    // preparation de la requete

    if ($produit != "") {
        $tprod = new Produit();
        $tprod->charger_id($produit);
        $rubrique = $tprod->rubrique;
    }

    if ($rubrique != "") $search .= " and rubrique=\"$rubrique\"";
    if ($id != "") $search .= " and caracteristique in($id)";
    if ($exclusion != "") $search .= " and caracteristique not in($exclusion)";


    $rubcaracteristique = new Rubcaracteristique();
    $tmpcaracteristique = new Caracteristique();
    $tmpcaracteristiquedesc = new Caracteristiquedesc();


    $order = "order by $tmpcaracteristique->table.classement";

    $query = "select DISTINCT(caracteristique) from $rubcaracteristique->table,$tmpcaracteristique->table  where 1 $search and $rubcaracteristique->table.caracteristique=$tmpcaracteristique->table.id $order";
    //if($id != "") $query = "select * from $tmpcaracteristique->table where 1 $search";

    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    $compt = 1;

    foreach ($resul as $row) {

        if ($courante == "1" && ($id != $caracteristique && !strstr($caracteristique, $id . "-")))
            continue;

        else if ($courante == "0" && ($id == $caracteristique || strstr($caracteristique, $id . "-")))
            continue;

        $tmpcaracteristiquedesc->charger($row->caracteristique);
        $temp = str_replace("#ID", "$row->caracteristique", $texte);

        $tmpcaracteristique->charger($tmpcaracteristiquedesc->caracteristique);

        if ($tmpcaracteristique->affiche == "0" && $affiche == "1") continue;

        $temp = str_replace("#TITRE", "$tmpcaracteristiquedesc->titre", $temp);
        $temp = str_replace("#CHAPO", "$tmpcaracteristiquedesc->chapo", $temp);
        $temp = str_replace("#DESCRIPTION", "$tmpcaracteristiquedesc->description", $temp);
        $temp = str_replace("#PRODUIT", "$produit", $temp);
        $temp = str_replace("#COMPT", "$compt", $temp);

        $compt++;

        $res .= $temp;
    }

    return $res;
}


function boucleCaracdisp($texte, $args) {

    global $caracdisp;

    $caracteristique = lireTag($args, "caracteristique", "int");

    /* DEBUT CODE DEPRECIE 1.5.2 par roadster31 */
    $stockmini = lireTag($args, "stockmini", "int");
    /* FIN CODE DEPRECIE */

    $avecproduit = lireTag($args, "avecproduit", "string");
    $avecproduitenstock = lireTag($args, "avecproduitenstock", "int");

    $courante = lireTag($args, "courante", "int");
    $rubrique = lireTag($args, "rubrique", "int");
    $classement = lireTag($args, "classement", "string");
    $aleatoire = lireTag($args, "aleatoire", "int");

    $deb = lireTag($args, "deb", "int");
    $num = lireTag($args, "num", "int");

    $id = lireTag($args, "caracdisp", "int_list");

    if ($id == "")
        $id = lireTag($args, "id", "int");

    $liste = "";
    $res = "";
    $search = "";
    $limit = "";

    $tcaracdisp = new Caracdisp();
    $tcaracdispdesc = new Caracdispdesc();

    // preparation de la requete
    if ($caracteristique != "") $search .= " and $tcaracdisp->table.caracteristique=\"$caracteristique\"";
    if ($id != "") $search .= " and $tcaracdisp->table.id IN ($id)";
    if ($aleatoire) $order = "order by " . " RAND()";
    else if ($classement == "alpha") $order = "order by $tcaracdispdesc->table.titre";
    else if ($classement == "alphainv") $order = "order by $tcaracdispdesc->table.titre desc";
    else if ($classement == "manuel") $order = "order by $tcaracdispdesc->table.classement";
    else if ($classement == "manuel") $order = "order by $tcaracdispdesc->table.classement";

    if ($deb == "")
        $deb = 0;

    if ($num != "")
        $limit = "limit $deb,$num";

    $joinavecproduit="";

    if ($avecproduit == "oui"){
        $caracvalch = new Caracval();
        $prod = new Produit();
        $joinavecproduit .= " left join $caracvalch->table on $tcaracdisp->table.id=$caracvalch->table.caracdisp ";
        $joinavecproduit .= " left join $prod->table on $caracvalch->table.produit=$prod->table.id ";
        $search .= " and $prod->table.ligne=1 ";
        if($avecproduitenstock != "") $search .= " and $prod->table.stock>=$avecproduitenstock ";
    }

    if (ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE) {
        // On retourne uniquement les caracdisp traduites
        $search .= " and $tcaracdispdesc->table.id is not null";
    }

    $query = "
			select distinct $tcaracdisp->table.id,$tcaracdisp->table.caracteristique from $tcaracdisp->table
			left join $tcaracdispdesc->table on $tcaracdispdesc->table.caracdisp = $tcaracdisp->table.id and $tcaracdispdesc->table.lang = " . ActionsLang::instance()->get_id_langue_courante() . "
			$joinavecproduit
      where 1 $search
			$order
			$limit
		";

    $resul = CacheBase::getCache()->query($query);

    if (!empty($resul)) {

        $compt = 1;

        foreach ($resul as $row) {

            if ($courante == "1" && ($id != $caracdisp && !strstr($caracdisp, "-" . $id)))
                continue;
            else if ($courante == "0" && ($id == $caracdisp || strstr($caracdisp, "-" . $id)))
                continue;

            /* DEBUT CODE DEPRECIE 1.5.2 par roadster31
                 *
                 * Le stock n'a pas vraiement de sens dans le contexte des caracteristiques. Info de Yoan le 28/11 :
                 * "Je me demande si ce n'est pas un vieux truc qui permettait de générer des menus particuliers en disant combien de produits avaient "cette caractéristique".
                 * Je t'avoue que là je ne vois pas trop l'intérêt non plus, sur le coup."
                 *
                 */
            if ($stockmini != "") {
                $caracvalch = new Caracval();
                $prod = new Produit();

                $querych = "select count(*) as nb
                    from $prod->table,$caracvalch->table
                    where $prod->table.id=$caracvalch->table.produit and $prod->table.ligne=1 and $caracvalch->table.caracdisp='" . $row->id . "'";
                $resulch = CacheBase::getCache()->query($querych);

                if ($resulch[0]->nb < $stockmini) continue;
            }
            /* FIN CODE DEPRECIE */

            $tcaracdispdesc->charger_caracdisp($row->id);
            $tcaracdisp->charger($row->id);

            $id = $row->id . "-";
            $caracteristique = $tcaracdisp->caracteristique . "-";

            if ($caracteristique == "$tcaracdisp->caracteristique" . "-" && $caracdisp == $row->id . "-")
                $selected = 'selected="selected"'; else $selected = "";

            $temp = str_replace("#IDC", $id, $texte);
            $temp = str_replace("#ID", $tcaracdisp->id, $temp);
            $temp = str_replace("#RUBRIQUE", "$rubrique", $temp);
            $temp = str_replace("#CARACTERISTIQUE", $tcaracdisp->caracteristique, $temp);
            $temp = str_replace("#CARACTERISTIQUEC", $caracteristique, $temp);
            $temp = str_replace("#TITRE", "$tcaracdispdesc->titre", $temp);
            $temp = str_replace("#SELECTED", "$selected", $temp);
            $temp = str_replace("#COMPT", $compt, $temp);
            $temp = str_replace("#NBRES", count($resul), $temp);
            $res .= $temp;

            $compt++;
        }
    }

    return $res;
}


function boucleCaracval($texte, $args) {

    $produit = lireTag($args, "produit", "int");
    $caracteristique = lireTag($args, "caracteristique", "int");
    $valeur = lireTag($args, "valeur", "string+\s\'");
    $classement = lireTag($args, "classement", "string");
    $article = lireTag($args, "article", "int");
    $aleatoire = lireTag($args, "aleatoire", "int");

    if ($produit == "" || $caracteristique == "") return "";

    if (substr($valeur, 0, 1) == "!") {
        $different = true;
        $valeur = substr($valeur, 1);
    } else {
        $different = false;
    }

    $search = $res = $order = $where = "";

    if ($aleatoire) $order = "order by " . " RAND()";
    else if ($classement == "caracdisp")
        $order = "ORDER BY cv.caracdisp";
    else if ($classement == "alpha")
        $order = "ORDER BY strval";
    else if ($classement == "alphainv")
        $order = "ORDER BY strval desc";
    else if ($classement == "manuel")
        $order = "ORDER BY cd.classement";

    $caracval = new Caracval();
    $caracdispdesc = new Caracdispdesc();

    if (!empty($valeur)) {

        $oper = $different ? '<>' : '=';

        $where = "
				AND (
				   (cd.titre IS NULL AND cv.valeur $oper '$valeur')
				   OR
				   (cd.titre IS NOT NULL AND cv.caracdisp $oper " . intval($valeur) . ")
				)
			";
    }

    $query = "
			SELECT
				*,IF(ISNULL(cd.titre), cv.valeur, cd.titre) as strval
			FROM
				$caracval->table cv
			LEFT JOIN
				$caracdispdesc->table cd on cd.caracdisp = cv.caracdisp and cd.lang = " . ActionsLang::instance()->get_id_langue_courante() . "
			WHERE
				cv.produit=" . intval($produit) . "
			AND
				cv.caracteristique=" . intval($caracteristique) . "
			$where
			$order
		";

    // $query = "select * from $caracval->table where 1 $search $order";
    $resul = CacheBase::getCache()->query($query);

    if (empty($resul)) return "";

    foreach ($resul as $row) {
        if (empty($row->strval)) {
            continue;
        }

        $temp = str_replace("#ID", $row->id, $texte);
        $temp = str_replace("#CARACDISP", $row->caracdisp, $temp);
        $temp = str_replace("#VALEUR", $row->strval, $temp);

        $prodtemp = new Produit();
        $prodtemp->charger_id($produit);

        $temp = str_replace("#RUBRIQUE", $prodtemp->rubrique, $temp);
        $temp = str_replace("#REF", $prodtemp->ref, $temp);

        $caractemp = new Caracteristiquedesc($row->caracteristique);

        $temp = str_replace("#TITRECARAC", $caractemp->titre, $temp);
        $temp = str_replace("#PRODUIT", $prodtemp->id, $temp);
        $temp = str_replace("#ARTICLE", $article, $temp);
        $temp = str_replace("#CARACTERISTIQUE", $caracteristique, $temp);

        $res .= $temp;
    }

    return $res;
}

function boucleAdresse($texte, $args) {

    $adresse = new Adresse();


    // récupération des arguments

    $adresse_id = lireTag($args, "id", "int");
    if ($adresse_id == '') $adresse_id = lireTag($args, "adresse", "int");

    $client_id = lireTag($args, "client", "int");
    $defaut = lireTag($args, "defaut", "int");
    $exclusion = lireTag($args, "exclusion", "int_list");

    $search = "";
    $res = "";

    // preparation de la requete
    if ($adresse_id != "") $search .= " and id=\"$adresse_id\"";
    if ($client_id != "") $search .= " and client=\"$client_id\"";
    if ($exclusion != "") $search .= " and id not in ($exclusion)";

    // éviter de lister l'ensemble des adresses de la base
    if ($adresse_id == "" && $client_id == "")
        return "";

    if ($defaut == "1" && $adresse_id != "0")
        return "";

    else if ($defaut == "0" && $adresse_id == "0")
        return "";

    if ($adresse_id != "0") {
        $query = "select * from $adresse->table where 1 $search";

        $resul = CacheBase::getCache()->query($query);
        if (empty($resul)) return "";

        foreach ($resul as $row) {

            // Les #RAISONnF
            $raisons = CacheBase::getCache()->query("select id from " . Raison::TABLE);

            if ($raisons) foreach ($raisons as $raison) {

                $sel = ($row->raison == $raison->id) ? 'selected="selected"' : '';

                $texte = str_replace("#RAISON" . $raison->id . "F", $sel, $texte);
            }

            $raisondesc = new Raisondesc();
            $raisondesc->charger($row->raison);

            $temp = str_replace("#ID", "$row->id", $texte);
            $temp = str_replace("#PRENOM", "$row->prenom", $temp);
            $temp = str_replace("#NOM", "$row->nom", $temp);
            $temp = str_replace("#ENTREPRISE", "$row->entreprise", $temp);
            $temp = str_replace("#RAISONID", $row->raison, $temp);
            $temp = str_replace("#RAISON", $raisondesc->long, $temp);
            $temp = str_replace("#LIBELLE", "$row->libelle", $temp);
            $temp = str_replace("#ADRESSE1", "$row->adresse1", $temp);
            $temp = str_replace("#ADRESSE2", "$row->adresse2", $temp);
            $temp = str_replace("#ADRESSE3", "$row->adresse3", $temp);
            $temp = str_replace("#CPOSTAL", "$row->cpostal", $temp);
            $temp = str_replace("#PAYS", "$row->pays", $temp);
            $temp = str_replace("#VILLE", "$row->ville", $temp);
            $temp = str_replace("#TEL", "$row->tel", $temp);
            $temp = str_replace("#TELFIXE", "$row->tel", $temp);
            $temp = str_replace("#TELPORT", "$row->tel", $temp);
            $temp = str_replace("#SUPPRURL", urlfond("livraison_adresse", "action=supprimerlivraison&amp;id=$row->id", true), $temp);
            $temp = str_replace("#URL", urlfond("commande", "action=modadresse&amp;adresse=$row->id", true), $temp);

            $res .= $temp;
        }
    } else {

        // Les #RAISONnF
        $raisons = CacheBase::getCache()->query("select id from " . Raison::TABLE);

        if ($raisons) foreach ($raisons as $raison) {

            $sel = ($_SESSION['navig']->client->raison == $raison->id) ? 'selected="selected"' : '';

            $texte = str_replace("#RAISON" . $raison->id . "F", $sel, $texte);
        }

        $raisondesc = new Raisondesc($_SESSION['navig']->client->raison);

        $temp = $texte;

        $temp = str_replace("#ID", $_SESSION['navig']->client->id, $temp);
        $temp = str_replace("#LIBELLE", "", $temp);
        $temp = str_replace("#RAISONID", $_SESSION['navig']->client->raison, $temp);
        $temp = str_replace("#RAISON", $raisondesc->long, $temp);
        $temp = str_replace("#NOM", $_SESSION['navig']->client->nom, $temp);
        $temp = str_replace("#PRENOM", $_SESSION['navig']->client->prenom, $temp);
        $temp = str_replace("#ENTREPRISE", $_SESSION['navig']->client->entreprise, $temp);
        $temp = str_replace("#ADRESSE1", $_SESSION['navig']->client->adresse1, $temp);
        $temp = str_replace("#ADRESSE2", $_SESSION['navig']->client->adresse2, $temp);
        $temp = str_replace("#ADRESSE3", $_SESSION['navig']->client->adresse3, $temp);
        $temp = str_replace("#CPOSTAL", $_SESSION['navig']->client->cpostal, $temp);
        $temp = str_replace("#VILLE", $_SESSION['navig']->client->ville, $temp);
        $temp = str_replace("#PAYS", $_SESSION['navig']->client->pays, $temp);
        $temp = str_replace("#EMAIL", $_SESSION['navig']->client->email, $temp);
        $temp = str_replace("#TELFIXE", $_SESSION['navig']->client->telfixe, $temp);
        $temp = str_replace("#TELPORT", $_SESSION['navig']->client->telport, $temp);
        if (empty($_SESSION['navig']->client->telport)) {
            $temp = str_replace("#TEL", $_SESSION['navig']->client->telfixe, $temp);
        } else {
            $temp = str_replace("#TEL", $_SESSION['navig']->client->telport, $temp);
        }

        $res .= $temp;

    }

    return $res;

}

function boucleVenteadr($texte, $args) {

    $venteadr = new Venteadr();


    // récupération des arguments

    $id = lireTag($args, "id", "int");

    $search = "";
    $res = "";

    // preparation de la requete
    if ($id != "") $search .= " and id=\"$id\"";

    $query = "select * from $venteadr->table where 1 $search";
    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    foreach ($resul as $row) {

        // Les #RAISONnF
        $raisons = CacheBase::getCache()->query("select id from " . Raison::TABLE);

        if ($raisons) foreach ($raisons as $raison) {

            $sel = ($row->raison == $raison->id) ? 'selected="selected"' : '';

            $texte = str_replace("#RAISON" . $raison->id . "F", $sel, $texte);
        }

        $raisondesc = new Raisondesc($row->raison);

        $temp = str_replace("#ID", "$row->id", $texte);
        $temp = str_replace("#PRENOM", "$row->prenom", $temp);
        $temp = str_replace("#NOM", "$row->nom", $temp);
        $temp = str_replace("#RAISONID", $row->raison, $temp);
        $temp = str_replace("#RAISON", $raisondesc->long, $temp);
        $temp = str_replace("#ADRESSE1", "$row->adresse1", $temp);
        $temp = str_replace("#ADRESSE2", "$row->adresse2", $temp);
        $temp = str_replace("#ADRESSE3", "$row->adresse3", $temp);
        $temp = str_replace("#CPOSTAL", "$row->cpostal", $temp);
        $temp = str_replace("#PAYS", "$row->pays", $temp);
        $temp = str_replace("#VILLE", "$row->ville", $temp);
        $temp = str_replace("#TEL", "$row->tel", $temp);
        $temp = str_replace("#ENTREPRISE", "$row->entreprise", $temp);
        $res .= $temp;
    }

    return $res;

}


function boucleCommande($texte, $args) {

    $commande = new Commande();


    // récupération des arguments
    $commande_id = lireTag($args, "id", "int");
    $commande_ref = lireTag($args, "ref", "string");
    $client_id = lireTag($args, "client", "int");
    $statut = lireTag($args, "statut", "string");
    $classement = lireTag($args, "classement", "string");
    $statutexcl = lireTag($args, "statutexcl", "int_list");
    $deb = lireTag($args, "deb", "int");
    $num = lireTag($args, "num", "int");

    if ($commande_ref == "" && $client_id == "") return;

    $search = "";
    $order = "";
    $limit = "";
    $res = "";

    // preparation de la requete
    if ($commande_id != "") $search .= " and id=\"$commande_id\"";
    if ($commande_ref != "") $search .= " and ref=\"$commande_ref\"";
    if ($client_id != "") $search .= " and client=\"$client_id\"";
    if ($statutexcl != "") $search .= " and statut not in ($statutexcl)";
    if ($statut != "" && $statut != "paye") $search .= " and statut=\"$statut\"";
    else if ($statut == "paye") $search .= " and statut>\"1\" and statut<>\"5\"";

    if ($deb == "") $deb = 0;

    if ($num != "") $limit = "limit $deb,$num";

    if ($classement == "inverse")
        $order = "order by date";
    else $order = "order by date desc";

    $query = "select * from $commande->table where 1 $search $order $limit";
    $statutdesc = new Statutdesc();
    $venteprod = new Venteprod();
    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    foreach ($resul as $row) {

        $jour = substr($row->date, 8, 2);
        $mois = substr($row->date, 5, 2);
        $annee = substr($row->date, 0, 4);

        $heure = substr($row->date, 11, 2);
        $minute = substr($row->date, 14, 2);
        $seconde = substr($row->date, 17, 2);

        $jour_livraison = substr($row->datelivraison, 8, 2);
        $mois_livraison = substr($row->datelivraison, 5, 2);
        $annee_livraison = substr($row->datelivraison, 0, 4);

        $datelivraison = $jour_livraison . "/" . $mois_livraison . "/" . $annee_livraison;

        $datefacturation = ($row->datefact == '0000-00-00') ? '' : substr($row->datefact, 8, 2) . "/" . substr($row->datefact, 5, 2) . "/" . substr($row->datefact, 0, 4);


        $query2 = "
					SELECT
						sum(prixu*quantite) as totalttc,
						sum(prixu*quantite / (1 + tva / 100)) as totalht
					FROM
						$venteprod->table
					where
						commande='$row->id'
				";

        $resul2 = CacheBase::getCache()->query($query2);

        $totalarticlesttc = $resul2[0]->totalttc;
        $totalarticlesht = $resul2[0]->totalht;

        if ($totalarticlesttc != 0)
            $pourcremise = $row->remise / $totalarticlesttc * 100;
        else
            $pourcremise = 0;

        $total = $totalarticlesttc - $row->remise;

        $port = $row->port;
        $totcmdport = $row->port + $total;

        $statutdesc->charger($row->statut);

        $temp = str_replace("#ID", "$row->id", $texte);
        $temp = str_replace("#ADRESSE", "$row->adrfact", $temp);
        $temp = str_replace("#ADRFACT", "$row->adrfact", $temp);
        $temp = str_replace("#ADRLIVR", "$row->adrlivr", $temp);

        if ($jour_livraison != "00")
            $temp = str_replace("#DATELIVRAISON", $jour_livraison . "/" . $mois_livraison . "/" . $annee_livraison, $temp);
        else
            $temp = str_replace("#DATELIVRAISON", "", $temp);
        $temp = str_replace("#DATEFACTURATION", $datefacturation, $temp);
        $temp = str_replace("#DATE", $jour . "/" . $mois . "/" . $annee, $temp);
        $temp = str_replace("#REF", "$row->ref", $temp);
        $temp = str_replace("#ADRFACT", "$row->adrfact", $temp);
        $temp = str_replace("#ADRLIVR", "$row->adrlivr", $temp);
        $temp = str_replace("#FACTURE", "$row->facture", $temp);
        $temp = str_replace("#TRANSACTION", "$row->transaction", $temp);
        $temp = str_replace("#REMISE", formatter_somme($row->remise), $temp);
        $temp = str_replace("#STATUTID", "$row->statut", $temp);
        $temp = str_replace("#STATUT", "$statutdesc->titre", $temp);
        $temp = str_replace("#PORT", formatter_somme($port), $temp);
        $temp = str_replace("#COMDEVISE", "$row->devise", $temp);
        $temp = str_replace("#TAUX", "$row->taux", $temp);
        $temp = str_replace("#COLIS", "$row->colis", $temp);
        $temp = str_replace("#LIVRAISON", "$row->livraison", $temp);

        $temp = str_replace("#CLIENT", "$row->client", $temp);
        $temp = str_replace("#TOTALARTICLESTTC", formatter_somme($totalarticlesttc), $temp);
        $temp = str_replace("#TOTALARTICLESHT", formatter_somme($totalarticlesht), $temp);
        $temp = str_replace("#POURCEREMISE", $pourcremise, $temp);
        $temp = str_replace("#TOTALCMD", formatter_somme($total), $temp);
        $temp = str_replace("#TOTCMDPORT", formatter_somme($totcmdport), $temp);

        $module = new Modules();
        $moduledesc = new Modulesdesc();

        $module->charger_id($row->transport);
        $moduledesc->charger($module->nom);
        $temp = str_replace("#TRANSPORTTITRE", $moduledesc->titre, $temp);

        $module->charger_id($row->paiement);
        $moduledesc->charger($module->nom);
        $temp = str_replace("#PAIEMENTTITRE", $moduledesc->titre, $temp);

        $temp = str_replace("#PAIEMENT", "$row->paiement", $temp);
        $temp = str_replace("#TRANSPORT", "$row->transport", $temp);

        $res .= $temp;
    }


    return $res;

}

function boucleTva($texte, $args) {

    $res = "";
    $commande_id = lireTag($args, "commande", "int");

    if (!empty($commande_id)) {

        $commande = new Commande();

        if ($commande->charger($commande_id)) {
            $venteprod = new Venteprod();

            $query = "
					select
						sum(prixu * quantite) as totalttc,
						tva
					from
						$venteprod->table
					where
						commande=$commande_id
					group by
						tva
				";

            $resul = CacheBase::getCache()->query($query);

            if (!empty($resul)) {
                foreach ($resul as $row) {

                    if (floatval($row->tva) > 0) {
                        $tmp = $texte;

                        $totalht = $row->totalttc / (1 + $row->tva / 100);
                        $montant = $row->totalttc - $totalht;

                        $tmp = str_replace("#TAUX", $row->tva, $tmp);
                        $tmp = str_replace("#MONTANT", formatter_somme($montant), $tmp);
                        $tmp = str_replace("#TOTALHT", formatter_somme($totalht), $tmp);
                        $tmp = str_replace("#TOTALTTC", formatter_somme($row->totalttc), $tmp);

                        $res .= $tmp;
                    }
                }
            }
        }
    }

    return $res;
}

function boucleVenteprod($texte, $args) {

    // récupération des arguments
    $commande_id = lireTag($args, "commande", "int");
    $produit = lireTag($args, "produit", "string");
    $parent = lireTag($args, "parent", "int");

    $search = "";
    $res = "";

    // preparation de la requete
    if ($commande_id != "") $search .= " and commande=\"$commande_id\"";
    if ($produit != "") $search .= " and ref=\"$produit\"";
    if ($parent != "") $search .= " and parent=\"$parent\"";

    $venteprod = new Venteprod();

    $query = "select * from $venteprod->table where 1 $search";
    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    $appliquer_tva = 0;

    foreach ($resul as $row) {

        $totalprod = $row->prixu * $row->quantite;

        $query2 = "select count(*) as nbvente from $venteprod->table where ref=\"" . $row->ref . "\"";
        $resul2 = CacheBase::getCache()->query($query2);
        $nbvente = $resul2[0]->nbvente;

        $temp = str_replace("#ID", "$row->id", $texte);
        $temp = str_replace("#COMMANDE", "$row->commande", $temp);
        $temp = str_replace("#REF", "$row->ref", $temp);
        $temp = str_replace("#TITRE", "$row->titre", $temp);
        $temp = str_replace("#CHAPO", "$row->chapo", $temp);
        $temp = str_replace("#DESCRIPTION", "$row->description", $temp);
        $temp = str_replace("#QUANTITE", "$row->quantite", $temp);
        $temp = str_replace("#TVA", "$row->tva", $temp);

        $prixht = $row->prixu / (1 + $row->tva / 100);

        $montanttva = $row->prixu - $prixht;
        $totalprodht = $prixht * $row->quantite;

        $temp = str_replace("#MONTANTTVA", formatter_somme($montanttva), $temp);
        $temp = str_replace("#PRIXUHT", formatter_somme($prixht), $temp);
        $temp = str_replace("#TOTALPRODHT", formatter_somme($totalprodht), $temp);

        $temp = str_replace("#PRIXU", formatter_somme($row->prixu), $temp);
        $temp = str_replace("#TOTALPROD", formatter_somme($totalprod), $temp);

        $temp = str_replace("#PARENT", $row->parent, $temp);

        $res .= $temp;
    }


    return $res;

}

function boucleTransport($texte, $args) {

    // récupération des arguments

    $id = lireTag($args, "id", "int_list");
    $nom = lireTag($args, "nom", "string");
    $exclusion = lireTag($args, "exclusion", "string_list");
    $idpays = intval(lireTag($args, "pays", "int"));
    $cpostal = intval(lireTag($args, "cpostal", "string"));
    $montantmini = intval(lireTag($args, "montantmini", "float"));

    $search = "";
    $res = "";

    if ($id != "") $search .= "and id in ($id)";
    if ($nom != "") $search .= "and nom=\"$nom\"";
    if ($exclusion != "") {
        $liste = "";
        $tabexcl = explode(",", $exclusion);
        for ($i = 0; $i < count($tabexcl); $i++)
            $liste .= "'" . $tabexcl[$i] . "'" . ",";

        $liste = rtrim($liste, ",");

        $search .= " and nom not in ($liste)";
    }

    $modules = new Modules();

    $query = "select * from $modules->table where type='2' and actif='1' $search order by classement";

    $resul = CacheBase::getCache()->query($query);
    if (empty($resul)) return "";

    $pays = new Pays();

    if ($idpays > 0) {
        $pays->charger($idpays);
    } else if ($_SESSION['navig']->adresse != "" && $_SESSION['navig']->adresse != 0) {
        $adr = new Adresse();
        $adr->charger($_SESSION['navig']->adresse);
        $pays->charger($adr->pays);
    } else
        $pays->charger($_SESSION['navig']->client->pays);

    $transzone = new Transzone();

    $compt = 0;

    foreach ($resul as $row) {

        if (!$transzone->charger($row->id, $pays->zone)) continue;

        /*$compt++;*/

        $modules = new Modules();
        $modules->charger_id($row->id);

        try {
            $instance = ActionsModules::instance()->instancier($modules->nom);
            $port = round(port($row->id, $pays->id, $cpostal), 2);
            $titre = $instance->getTitre();
            $chapo = $instance->getChapo();
            $description = $instance->getDescription();
        } catch (Exception $ex) {
            $titre = $chapo = $description = '';
        }

        if ($port < $montantmini) continue;

        $compt ++;

        // Chercher le logo
        $exts = array('png', 'gif', 'jpeg', 'jpg');
        $logo = false;
        foreach ($exts as $ext) {
            $tmp = ActionsModules::instance()->lire_chemin_base() . "/$row->nom/logo.$ext";
            if (file_exists($tmp)) {
                $logo = ActionsModules::instance()->lire_url_base() . "/$row->nom/logo.$ext";
                break;
            }
        }

        $temp = str_replace("#NOM", $row->nom, $texte);
        $temp = str_replace("#TITRE", "$titre", $temp);
        $temp = str_replace("#CHAPO", "$chapo", $temp);
        $temp = str_replace("#DESCRIPTION", "$description", $temp);
        $temp = str_replace("#URLCMD", urlfond("commande", "action=transport&amp;id=" . $row->id, true), $temp);
        $temp = str_replace("#ID", "$row->id", $temp);
        $temp = str_replace("#LOGO", $logo, $temp);
        $temp = str_replace("#PORT", formatter_somme($port), $temp);
        $temp = str_replace("#COMPT", "$compt", $temp);

        $res .= $temp;

    }


    return $res;

}


function boucleRSS($texte, $args) {
    @ini_set('default_socket_timeout', 5);

    // récupération des arguments
    $url = lireTag($args, "url", "string+\/:.");
    $nb = lireTag($args, "nb", "int");
    $deb = lireTag($args, "deb", "int");
    $i = 0;
    $compt = 0;

    if ($url == "") return;

    $feed = new SimplePie();
    $feed->set_feed_url($url);

    $rss_cache = FICHIER_URL.'/client/cache/rss';

    if (!is_dir($rss_cache)) mkdir($rss_cache);
    $feed->set_cache_location($rss_cache);

    $feed->init();
    $feed->handle_content_type();

    $chantitle = $feed->get_title();
    $chanlink = $feed->get_permalink();
    $res = '';
    foreach ($feed->get_items() as $item) {
        if ($compt < $deb) {
            $compt++;
            continue;
        }

        $link = $item->get_permalink();
        $title = strip_tags($item->get_title());
        $author = strip_tags($item->get_author());
        $description = strip_tags($item->get_description());
        $dateh = $item->get_date('j F Y | g:i a');
        $jour = $item->get_date('j');
        $mois = $item->get_date('F');
        $annee = $item->get_date('Y');
        $heure = $item->get_date('g');
        $minute = $item->get_date('i');
        $seconde = $item->get_date('a');
        $temp = str_replace("#SALON", "$chantitle", $texte);
        $temp = str_replace("#WEB", "$chanlink", $temp);
        $temp = str_replace("#TITRE", "$title", $temp);
        $temp = str_replace("#LIEN", "$link", $temp);
        $temp = str_replace("#DESCRIPTION", "$description", $temp);
        $temp = str_replace("#AUTEUR", "$author", $temp);
        $temp = str_replace("#DATE", "$jour/$mois/$annee", $temp);
        $temp = str_replace("#HEURE", "$heure:$minute $seconde", $temp);

        $i++;
        $res .= $temp;
        if ($i == $nb) return $res;
    }

    return $res;
}


function boucleDeclinaison($texte, $args) {

    global $declinaison;

    $id = lireTag($args, "id", "int_list");
    $rubrique = lireTag($args, "rubrique", "int");
    $produit = lireTag($args, "produit", "int");
    $courante = lireTag($args, "courante", "int");
    $exclusion = lireTag($args, "exclusion", "int_list");
    $classement = lireTag($args, "classement", "string");
    $stockmini = lireTag($args, "stockmini", "int");

    $search = "";
    $res = "";

    // preparation de la requete
    if ($rubrique != "") $search .= " and rubrique=\"$rubrique\"";
    if ($id != "") $search .= " and dcl.id in ($id)";
    if ($exclusion != "") $search .= " and dcl.id not in ($exclusion)";

    if ($classement == "alpha") $order = "order by dcd.titre";
    else if ($classement == "alphainv") $order = "order by dcd.titre desc";
    else $order = "order by dcl.classement asc";

    if (ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE) {
        // On retourne uniquement les declinaisons traduites
        $search .= " and dcd.id is not null";
    }

    $rubdeclinaison = new Rubdeclinaison();
    $tmpdeclinaison = new Declinaison();
    $tmpdeclinaisondesc = new Declinaisondesc();

    if ($rubrique == "") {
        $query = "
								SELECT
										dcl.id as iddeclinaison
								FROM
										$tmpdeclinaison->table dcl
								LEFT JOIN
										$tmpdeclinaisondesc->table dcd on dcd.declinaison = dcl.id and dcd.lang=" . ActionsLang::instance()->get_id_langue_courante() . "
								WHERE
										1 $search $order";
    } else {
        $query = "
								SELECT DISTINCT
										rub.declinaison as iddeclinaison
								FROM
										$rubdeclinaison->table rub
								LEFT JOIN
										$tmpdeclinaison->table dcl on dcl.id = rub.declinaison
								LEFT JOIN
										$tmpdeclinaisondesc->table dcd on dcd.declinaison = rub.declinaison and dcd.lang=" . ActionsLang::instance()->get_id_langue_courante() . "
								WHERE
										1 $search $order";
    }

    $resul = CacheBase::getCache()->query($query);

    if (empty($resul)) return "";

    $tmpdeclidisp = new Declidisp();
    $tmpstock = new Stock();
    $tmpexdecprod = new Exdecprod();

    foreach ($resul as $row) {

        if ($courante == "1" && ($row->iddeclinaison != $declinaison))
            continue;

        else if ($courante == "0" && ($row->iddeclinaison == $declinaison))
            continue;

        if ($stockmini > 0 && $produit > 0) {
            $query = "
										SELECT
												dd.id
										FROM
												$tmpstock->table s
										LEFT JOIN
												$tmpdeclidisp->table dd on dd.id = s.declidisp
										WHERE
												dd.declinaison = $row->iddeclinaison
										AND
												s.produit = $produit
										AND
												s.valeur >= $stockmini
										AND
												dd.id NOT IN (SELECT declidisp FROM $tmpexdecprod->table WHERE produit = $produit)
								";

            $resdeclidisp = CacheBase::getCache()->query($query);

            if (empty($resdeclidisp)) continue;
        }

        $declinaisondesc = new Declinaisondesc($row->iddeclinaison);

        $temp = str_replace("#ID", $row->iddeclinaison, $texte);
        $temp = str_replace("#TITRE", $declinaisondesc->titre, $temp);
        $temp = str_replace("#CHAPO", $declinaisondesc->chapo, $temp);
        $temp = str_replace("#DESCRIPTION", $declinaisondesc->description, $temp);
        $temp = str_replace("#PRODUIT", "$produit", $temp);

        $res .= $temp;
    }

    return $res;
}

function boucleDeclidisp($texte, $args) {

    global $declidisp;

    $declinaison = lireTag($args, "declinaison", "int");
    $id = lireTag($args, "id", "int");
    $produit = lireTag($args, "produit", "int");
    $classement = lireTag($args, "classement", "string");
    $stockmini = lireTag($args, "stockmini", "int");
    $courante = lireTag($args, "courante", "int");
    $num = lireTag($args, "num", "int");

    $search = "";
    $limit = "";
    $res = "";

    $tdeclidisp = new Declidisp();
    $tdeclidispdesc = new Declidispdesc();

    // preparation de la requete
    if ($declinaison != "") $search .= " and $tdeclidisp->table.declinaison=\"$declinaison\"";
    if ($id != "") $search .= " and $tdeclidisp->table.id=\"$id\"";

    if ($classement == "alpha") $order = "order by $tdeclidispdesc->table.titre";
    else if ($classement == "alphainv") $order = "order by $tdeclidispdesc->table.titre desc";
    else if ($classement == "manuel") $order = "order by $tdeclidispdesc->table.classement";

    if (ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE) {
        // On retourne uniquement les declidisp traduites
        $search .= " and $tdeclidispdesc->table.id is not null";
    }

    if ($stockmini != "" && $produit != "") {

        $stock = new Stock();

        $stock_join = "inner join $stock->table on $stock->table.produit=$produit and $stock->table.declidisp = declidisp.id";
        $search .= " and $stock->table.valeur >= $stockmini";
    }

    if ($num != "") {
        $limit = " limit $num";
    }

    $query = "
			select $tdeclidisp->table.* from $tdeclidisp->table
			$stock_join
			left join $tdeclidispdesc->table on $tdeclidispdesc->table.declidisp = $tdeclidisp->table.id and $tdeclidispdesc->table.lang = " . ActionsLang::instance()->get_id_langue_courante() . "
			where 1 $search
			$order
			$limit
		";

    $resul = CacheBase::getCache()->query($query);

    if (!empty($resul)) {

        $exdecprod = new Exdecprod();

        foreach ($resul as $row) {

            if ($courante == "1" && ($row->id . "-" != $declidisp))
                continue;

            else if ($courante == "0" && ($row->id . "-" == $declidisp))
                continue;

            if ($produit != "" && $exdecprod->charger($produit, $row->id)) continue;

            $tdeclidispdesc = new Declidispdesc($row->id);

            $temp = str_replace("#ID", $tdeclidispdesc->declidisp, $texte);
            $temp = str_replace("#DECLINAISON", $row->declinaison, $temp);
            $temp = str_replace("#TITRE", "$tdeclidispdesc->titre", $temp);
            $temp = str_replace("#PRODUIT", "$produit", $temp);

            $res .= $temp;
        }
    }

    return $res;
}


function boucleStock($texte, $args) {

    $declidisp = lireTag($args, "declidisp", "int");
    $produit = lireTag($args, "produit", "int");
    $article = lireTag($args, "article", "int");
    $declinaison = lireTag($args, "declinaison", "int");
    $res = '';
    if ($article != "")
        $produit = $_SESSION['navig']->panier->tabarticle[$article]->produit->id;

    if ($produit == "") return "";

    if ($article != "" && $declinaison)
        foreach ($_SESSION['navig']->panier->tabarticle[$article]->perso as $perso)
            if ($perso->declinaison == $declinaison)
                $declidisp = $perso->valeur;

    if ($declidisp != "") {
        $stock = new Stock($declidisp, $produit);
        $stock_dispo = $stock->valeur;
    } else {
        $tmpprod = new Produit();
        $tmpprod->charger_id($produit);
        $stock_dispo = $tmpprod->stock;
    }

    $tmpprod = new Produit();
    $tmpprod->charger_id($produit);

    $prix = $tmpprod->prix + $stock->surplus;
    $prix2 = $tmpprod->prix2 + $stock->surplus;

    $temp = str_replace("#ID", "$stock->id", $texte);
    $temp = str_replace("#PRIX2", "$prix2", $temp);
    $temp = str_replace("#PRIX", "$prix", $temp);
    $temp = str_replace("#SURPLUS", "$stock->surplus", $temp);
    $temp = str_replace("#DECLIDISP", "$declidisp", $temp);
    $temp = str_replace("#PRODUIT", "$produit", $temp);
    $temp = str_replace("#VALEUR", "$stock_dispo", $temp);
    $temp = str_replace("#ARTICLE", "$article", $temp);

    if (trim($temp) != "") $res .= $temp;

    return $res;
}


function boucleDecval($texte, $args) {

    $article = lireTag($args, "article", "int");
    $declinaison = lireTag($args, "declinaison", "int");
    $ref = lireTag($args, "ref", "string");

    if ($article == "") return "";

    $res = "";

    $tdeclinaison = new Declinaison();
    $tdeclinaisondesc = new Declinaisondesc();
    $tdeclidisp = new Declidisp();
    $tdeclidispdesc = new Declidispdesc();

    foreach ($_SESSION['navig']->panier->tabarticle[$article]->perso as $tperso) {

        if ($declinaison != "" && $declinaison != $tperso->declinaison)
            continue;

        $tdeclinaison->charger($tperso->declinaison);
        $tdeclinaisondesc->charger($tdeclinaison->id);

        // recup valeur declidisp ou string
        if ($tdeclinaison->isDeclidisp($tperso->declinaison)) {
            $tdeclidisp->charger($tperso->valeur);
            $tdeclidispdesc->charger_declidisp($tdeclidisp->id);
            $valeur = $tdeclidispdesc->titre;
        } else
            $valeur = $tperso->valeur;

        $temp = str_replace("#DECLITITRE", "$tdeclinaisondesc->titre", $texte);
        $temp = str_replace("#DECLINAISON", "$tdeclinaison->id", $temp);
        $temp = str_replace("#REF", "$ref", $temp);
        $temp = str_replace("#ARTICLE", "$article", $temp);
        $temp = str_replace("#VALEUR", "$valeur", $temp);
        $temp = str_replace("#DECLIDISP", "$tdeclidisp->id", $temp);

        $res .= $temp;
    }

    return $res;
}

function boucleReprisePaiement($texte, $args) {
    $paiement = lireTag($args, "paiement", "int");
    $refcommande = lireTag($args, "refcommande", "string");

    $module = new Modules();
    $commande = new Commande();

    $res = "";

    if (!empty($paiement) && $module->charger_id($paiement) && !empty($refcommande) && $commande->charger_ref($refcommande)) {
        if ($module->type == 1 && $module->actif == 1) {
            $res = str_replace("#URL", sprintf("index.php?action=reprise_paiement&amp;id_commande=%d&amp;id_paiement=%d", $commande->id, $module->id), $texte);
        }
    }

    return $res;
}

?>
