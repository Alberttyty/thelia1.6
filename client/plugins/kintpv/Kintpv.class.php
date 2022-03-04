<?php
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubrique.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdisp.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdispdesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Produitdesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../declibre/Declibre.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");

class KinTPV extends PluginsClassiques {

  public $retour = array();
  public $champs = array();
  private $nbArticleTraite = 0;

	public function KinTPV() {
		  $this->PluginsClassiques();
	}

  public function init() {

    	$var = new Variable();
  		if(!$var->charger("kintpv-rub-exclude")) {
    			$var->nom = "kintpv-rub-exclude";
    			$var->valeur = "";
    			$var->add();
  		}

      $var = new Variable();
  		if(!$var->charger("kintpv-carac-marque")) {
    			$var->nom = "kintpv-carac-marque";
    			$var->valeur = "0";
    			$var->add();
  		}

      $var = new Variable();
  		if(!$var->charger("kintpv-last-export")){
    			$var->nom = "kintpv-last-export";
    			$var->valeur = "";
          $var->cache = 1;
    			$var->add();
  		}

      $var = new Variable();
  		if(!$var->charger("kintpv-mail-import")){
    			$var->nom = "kintpv-mail-import";
    			$var->valeur = "";
    			$var->add();
  		}

      $var = new Variable();
  		if(!$var->charger("kintpv-rub-default")){
    			$var->nom = "kintpv-rub-default";
    			$var->valeur = "";
    			$var->add();
  		}

      $Message = new Message();
  		if(!$Message->charger("importkintpv")) {

    			$Message->nom = "importkintpv";
    			$lastid = $Message->add();

    			$Messagedesc = new Messagedesc();
    			$Messagedesc->message = $lastid;
    			$Messagedesc->lang = ActionsLang::instance()->get_id_langue_defaut();

    			$Messagedesc->intitule = "Mail envoyé à la fin de l'import KinTPV";
    			$Messagedesc->titre = "Import terminé";
    			$Messagedesc->chapo = "";
    			$Messagedesc->descriptiontext = "L'import du fichier KinTPV est terminé.\n\n"
    				                              . "Nombre d'articles traités avec succès : __NB_ARTICLES__ \n\n"
    				                              . "Erreur(s) : __ERREURS__\n";
    			$Messagedesc->description = nl2br($Messagedesc->descriptiontext);

    			$Messagedesc->add();
  		}

	}

  public function substitutions_mail($texte,$html=true) {

      $texte = str_replace("__NB_ARTICLES__", count($this->retour['traite']), $texte);
      $erreurs="Aucune";

      if(count($this->retour['erreur'])>0) {
          $erreurs=count($this->retour['erreur'])."";

          if($html) $erreurs.="<br/>";

          foreach ($this->retour['erreur'] as $k => $v) {
              if($html) $erreurs.="<br/>";
              $erreurs .= " ".$v;
          }
      }

      $texte = str_replace("__ERREURS__", $erreurs, $texte);
      return $texte;
  }

  public function traiterFichier($fichier) {
      $XML = simplexml_load_file($fichier);
      foreach($XML->IMPORT_ARTICLE as $Article) {
          $this->traiterArticle($Article);
          $this->nbArticleTraite += 1;
      }
      return $this->nbArticleTraite;
  }

  private function traiterArticle($Article) {
    $Produit    = new Produit();
    $Produitdesc= new Produitdesc();
    $Rubrique   = new Rubrique();
    $nouveau    = true;
    $declinaisons= false;
    $rub_defaut = new Variable();
    $rub_defaut->charger("kintpv-rub-default");

    if($Article->Declinaison != '') {
        $declinaisons = $this->creerTabDeclinaisons(strval($Article->Declinaison));
    }

    $ref = strval($Article->Reference_Article);

    if(!empty($ref) && intval($Article->PublierSurWeb_0_1) == 1) {

      if($Produit->charger($ref)) {
          $nouveau = false;
          $Produitdesc->charger($Produit->id);
      } else $Produit->ref = $ref;

      $Produitdesc->titre = strval($Article->Nom_Article);

      if($Article->Description_Complete != "") $Produitdesc->description = $this->preparerDesc(strval($Article->Description_Complete));

      // ON RANGE L'ARTICLE DANS LA BONNE RUBRIQUE
      if(!empty(intval($Article->Categorie_Web))) $Rubrique->getVars('SELECT * FROM '.$rubrique->table.' WHERE lien="'.strval($Article->Categorie_Web).'" LIMIT 0,1');
      else $Rubrique->charger(intval($rub_defaut->valeur));
      /*else {
          $lienRubParent = trim(strval($Article->Type_Article));
          $lienRubParent = strtolower($lienRubParent);
          $lienRubParent = str_replace(' ','',$lienRubParent);
          $Rubrique->getVars('SELECT * FROM '.$rubrique->table.' WHERE lien="'.$lienRubParent.'" LIMIT 0,1');
      }*/

      if(empty($Rubrique->id)) {
          $this->retour['erreur'][]="Erreur pour l'article \"".$ref." : ".$declinaisons[0]."\" la rubrique \"".strval($Article->Categorie_Web)."\" n'existe pas sur le site internet.";

      } else {
          $Produit->rubrique = $Rubrique->id;
          $Produit->tva = strval($Article->TauxTaxe_TVA);

          $Produit->datemodif = date("Y-m-d H:i:s");
          $Produit->ligne = 1;

          if(!$declinaisons) {
              $Produit->stock = intval($Article->QteEnStock);
              $Produit->prix = floatval($Article->PrixVente_TTC);
          }

          if($nouveau) {
              $idProduit = $Produit->add();
              $Produit->id = $idProduit;
              $Produitdesc->produit=$idProduit;
              $Produitdesc->lang=1;
              $Produitdesc->add();
              $Produitdesc->reecrire();

          } else {
              $idProduit = $Produit->id;
              $Produit->maj();
              $Produitdesc->lang=1;
              $Produitdesc->maj();
          }

          //GESTION DE LA MARQUE DE L'ARTICLE
          $Caracval     = new Caracval();
          $Caracdisp    = new Caracdisp();
          $Caracdispdesc= new Caracdispdesc();
          $carac_marque = new Variable();
          $carac_marque->charger("kintpv-carac-marque");

          $marque = strval($Article->MARQUE);
          $marque = ucfirst(strtolower($marque));
          if($marque != "") { //RECHERCHE DE LA MARQUE
              $caracQuery="SELECT $Caracdisp->table.id
                            FROM $Caracdisp->table
                            LEFT JOIN $Caracdispdesc->table ON ($Caracdisp->table.id=$Caracdispdesc->table.caracdisp)
                            WHERE $Caracdisp->table.caracteristique=$carac_marque->valeur
                            AND $Caracdispdesc->table.lang=1
                            AND $Caracdispdesc->table.titre=\"$marque\"
                            ORDER BY classement LIMIT 0,1";
              $caracdispId = $Caracdisp->query($caracQuery);
              $caracdispId = mysql_result($caracdispId,0);

              //SI LA MARQUE EXISTE
              if(!empty($caracdispId)) $Caracdisp->charger($caracdispId);
              else { // SINON ON L'AJOUTE
                  $Caracdisp->caracteristique = $carac_marque->valeur;
                  $caracdispId = $Caracdisp->add();
                  $Caracdisp->id = $caracdispId;
                  $Caracdispdesc->caracdisp = $caracdispId;
                  $Caracdispdesc->lang = 1;
                  $Caracdispdesc->titre = $marque;
                  $Caracdispdesc->add();
              }

              if(!empty($Caracdisp->id)) {
                  //ASSOCIATION DE LA MARQUE AU PRODUIT
                  if(!$Caracval->charger($idProduit,$carac_marque->valeur)) $nouvel_caracval=true;
                  else $nouvel_caracval=false;

                  $Caracval->produit=$idProduit;
                  $Caracval->caracteristique=$carac_marque->valeur;
                  $Caracval->caracdisp=$Caracdisp->id;

                  if($nouvel_caracval) $Caracval->add();
                  else $Caracval->maj();

              } else $this->retour['erreur'][]="Article \"$Produit->ref\" : la marque\"$marque\" n'a pu être ajoutée car la caractéristique \"Marque\" n'existe pas.";
          }

          if($declinaisons) {

              $Declibre = new Declibre();
              $nouvel_declibre = true;
              $lien = strval($Article->Reference_Article).$declinaisons[0];

              if($Declibre->charger_lien($lien)) $nouvel_declibre = false;
              else $Declibre->lien = $lien;

              $Declibre->ref = $ref;
              $Declibre->stock = intval($Article->QteEnStock);
              $Declibre->prix = floatval($Article->PrixVente_TTC);

              $Declibredesc = new Declibredesc();

              if($nouvel_declibre) {
                  $id_declibre = $Declibre->add();
                  $Declibre->id = $id_declibre;
                  $Declibredesc->declibre = $id_declibre;

              } else {
                  $id_declibre = $Declibre->id;
                  $Declibre->maj();
                  $Declibredesc->charger($id_declibre);
              }

              $Declibredesc->declinaison=$declinaisons[0];
              $Declibredesc->lang=1;

              if($nouvel_declibre) $Declibredesc->add();
              else $Declibredesc->maj();

              $Declibre->maj_stock($Produit);
              $Declibre->maj_prix($Produit);
              
          }

          if(!isset($this->retour['traite'])) $this->retour['traite']=array();
          if(!array_key_exists($Produit->id,$this->retour['traite'])) $this->retour['traite'][$Produit->id]=array();
          if($declinaisons) $this->retour['traite'][$Produit->id][]=$id_declibre;

      }

    }

  }

  /**
   * Renvoie les déclinaisons d'un produit sous la forme d'un tableau associatif
   * Stocke également les valeurs de déclinaisons sous une chaine de caractère utilisé pour la Référence produit ainsi que le Titre produit
   * @return array(tabDeclinaisons)
   * $tabDeclinaisons[0] = string Valeurs de déclinaisons.
   */
  private function creerTabDeclinaisons($strDeclinaisons) {
      // On initialise le tableau associatif
      $tabDeclinaisons = [];
      // La première entrée servira pour stocker les valauers sous la forme d'une chaine de caractère
      $tabDeclinaisons[0] = '';

      // GO
      $tabDeclis = explode(':',$strDeclinaisons);
      foreach($tabDeclis as $declinaison) {
          $decli = explode('=',$declinaison);
          $tabDeclinaisons[$decli[0]] = $decli[1];
          // Stockage des valeurs de déclinaisons sous forme de chaine de caractère sur la première ligne du tableau.
          $tabDeclinaisons[0] .= ':'.$decli[1];
      }

      return $tabDeclinaisons;
  }

  private function preparerDesc($texte) {

      $texte=$this->nettoyerChaine($texte, false);
      $texte="<p>".$texte."</p>";
      //les espaces en trop
      $texte=preg_replace('/\s\s+/',' ',$texte);
      //les saut de paragraphe
      $texte=preg_replace('/(\.{1}) (\w+)/','.</p><p>$2',$texte);
      //les listes
      $texte=preg_replace('/ (\*|-{1}) (\w+)/','<br/><span class="puce">-</span> $2',$texte);
      //les paragraphe vide
      $texte=preg_replace('/<p>\s+<\/p>/','',$texte);

      return $texte;
  }

  private function nettoyerChaine($chaine, $titre=true, $taille_vetement=false) {

      $chaine=utf8_encode($chaine);
      $chaine=trim($chaine);
      $chaine=ucfirst($chaine);
      if($titre) $chaine=mb_convert_case($chaine,MB_CASE_TITLE,'UTF-8');
      if($taille_vetement) {
          $patterns = array(
                            '/(^|\s)(xxs)\b/i',
                            '/(^|\s)(xs)\b/i',
                            '/(^|\s)(s)\b/i',
                            '/(^|\s)(m)\b/i',
                            '/(^|\s)(l)\b/i',
                            '/(^|\s)(xl)\b/i',
                            '/(^|\s)(xxl)\b/i',
                            '/(^|\s)(xxxl|3xl)\b/i',
                            '/(^|\s)(xxxxl|4xl)\b/i'
          );
          $replacements = array(
                            'XXS',
                            'XS',
                            'S',
                            'M',
                            'L',
                            'XL',
                            'XXL',
                            '3XL',
                            '4XL'
          );
          $chaine=preg_replace($patterns,$replacements,$chaine);
      }

      return $chaine;
  }

}

?>
