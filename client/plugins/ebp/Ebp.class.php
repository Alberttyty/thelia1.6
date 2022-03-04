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

class EBP extends PluginsClassiques {

  var $delimiter=";";
  var $enclosure="\"";
  var $debut=0;
  var $pas=25;
  var $fin=0;
  var $compteur=0;

  var $retour=array();
  var $champs=array();

	function EBP(){
		$this->PluginsClassiques();
	}

  function init(){

  	$var = new Variable();
		if(!$var->charger("ebp-rubrique-exclusion")){
			$var->nom = "ebp-rubrique-exclusion";
			$var->valeur = "";
			$var->add();
		}

    $var = new Variable();
		if(!$var->charger("ebp-caracteristique-marque")){
			$var->nom = "ebp-caracteristique-marque";
			$var->valeur = "0";
			$var->add();
		}

    $var = new Variable();
		if(!$var->charger("ebp-dernier-export")){
			$var->nom = "ebp-dernier-export";
			$var->valeur = "";
      $var->cache = 1;
			$var->add();
		}

    $var = new Variable();
		if(!$var->charger("ebp-mail-import")){
			$var->nom = "ebp-mail-import";
			$var->valeur = "";
			$var->add();
		}

    $message = new Message();

		if (! $message->charger("importebp")) {

			$message->nom = "importebp";

			$lastid = $message->add();

			$messagedesc = new Messagedesc();
			$messagedesc->message = $lastid;
			$messagedesc->lang = ActionsLang::instance()->get_id_langue_defaut();

			$messagedesc->intitule = "Mail envoyé à la fin de l'import EBP";
			$messagedesc->titre = "Import terminé";
			$messagedesc->chapo = "";
			$messagedesc->descriptiontext = "L'import du fichier EBP est terminé.\n\n"
				                              . "Nombre d'articles traités avec succès : __NB_ARTICLES__ \n\n"
				                              . "Erreur(s) : __ERREURS__\n";
			$messagedesc->description = nl2br($messagedesc->descriptiontext);

			$messagedesc->add();
		}

	}

  function traiterFichier($fichier,$debut,$fin){

    $file = new SplFileObject($fichier);
    if ($debut!=0) $file->seek($debut);

    $this->compteur=$debut;

    while (!$file->eof()&&$this->compteur<=$fin) {
        $ligne=$file->fgetcsv($this->delimiter,$this->enclosure);
        if ($debut==0&&$this->compteur==0){
          /*Premiere ligne on initialise les champs*/
          $this->masquerToutProduits();
          $this->initColonnes($ligne);
        }
        else {
          /*Deuxième ligne on traite les fichiers*/
          /*Si ligne non vide*/
          if(count($ligne)>1) $this->traiterLigne($ligne);
        }
        $this->compteur+=1;
    }

    if($file->eof()){
      //c'est fini
      $_POST['termine']=1;
    }

  }

  function traiterLigne($ligne){
    $produit = new Produit();
    $produitdesc = new Produitdesc();
    $rubrique = new Rubrique();
    $nouveau=true;
    $declinaison=false;
    if($this->getValeur($ligne,'CodeArtMaster')!='') $declinaison=true;
    if($declinaison) $ref=$this->getValeur($ligne,'CodeArtMaster');
    else $ref=$this->getValeur($ligne,'Code');

    if (($ref!="")&&(strtolower($this->getValeur($ligne,'bWeb'))=="oui")){

      if($produit->charger($ref)){
        $nouveau=false;
        $produitdesc->charger($produit->id);
      }
      else $produit->ref=$ref;

      $produitdesc->titre=$this->nettoyerChaine($this->getValeur($ligne,'Designation'),false);
      if($declinaison) $produitdesc->titre=preg_replace('#\((.+)\)#U','',$produitdesc->titre);
      if($this->getValeur($ligne,'Link38C7.Description')!="") $produitdesc->description=$this->preparerDesc($this->getValeur($ligne,'Link38C7.Description'));

      $rubrique->getVars("select * from $rubrique->table where lien=\"".$this->getValeur($ligne,'CodeCollection')."\" limit 0,1");
      $produit->rubrique=$rubrique->id;

      if($this->getValeur($ligne,'CodeCollection')==""){
        $this->retour['erreur'][]="Erreur pour l'article \"".$this->getValeur($ligne,'Code')." : ".$this->nettoyerChaine($this->getValeur($ligne,'Designation'),false)."\" aucune rubrique renseignée.";
      }
      elseif($rubrique->id==""){
        $this->retour['erreur'][]="Erreur pour l'article \"".$this->getValeur($ligne,'Code')." : ".$this->nettoyerChaine($this->getValeur($ligne,'Designation'),false)."\" la rubrique \"".$this->getValeur($ligne,'CodeCollection')."\" n'existe pas sur le site internet.";
      }
      else {

        $produit->tva=$this->getValeur($ligne,'TauxTVA');

        $produit->datemodif = date("Y-m-d H:i:s");
        $produit->ligne=1;

        if(!$declinaison) {
          $produit->stock=intval($this->getValeur($ligne,'ST.Qte'));
          $produit->prix=floatval(str_replace(',','.',$this->getValeur($ligne,'PxVenteTTC0')));
        }

        if($nouveau) {
          $id=$produit->add();
          $produit->id=$id;
          $produitdesc->produit=$id;
          $produitdesc->lang=1;
          $produitdesc->add();
          $produitdesc->reecrire();
        }
        else {
          $id=$produit->id;
          $produit->maj();
          $produitdesc->lang=1;
          $produitdesc->maj();
        }

        $caracval=new Caracval();
        $caracdisp=new Caracdisp();
        $caracdispdesc=new Caracdispdesc();
        $carac_marque = new Variable();
        $carac_marque->charger("ebp-caracteristique-marque");

        $marque=$this->nettoyerChaine($this->getValeur($ligne,'Famille.Libelle'),false);
        if($marque!=""){
          $carac_query="select $caracdisp->table.id from $caracdisp->table left join $caracdispdesc->table on ($caracdisp->table.id=$caracdispdesc->table.caracdisp) where $caracdisp->table.caracteristique=$carac_marque->valeur and $caracdispdesc->table.lang=1 and $caracdispdesc->table.titre=\"$marque\" order by classement limit 0,1";
          $carac_resul = $caracdisp->query($carac_query);
          $carac_row = $caracdisp->fetch_object($carac_resul);
          if(is_object($carac_row)){
            if(!$caracval->charger($id,$carac_marque->valeur)){
              $nouvel_caracval=true;
            }
            else{
              $nouvel_caracval=false;
            }
            $caracval->produit=$id;
            $caracval->caracteristique=$carac_marque->valeur;
            $caracval->caracdisp=$carac_row->id;
            if ($nouvel_caracval){
              $caracval->add();
            }
            else{
              $caracval->maj();
            }
          }
          else {
            $this->retour['erreur'][]="Article \"$produit->ref\" : la marque\"$marque\" n'existe pas dans la liste du site internet.";
          }
        }

        if($declinaison){

          $declibre=new Declibre();
          $nouvel_declibre=true;
          $lien=$this->getValeur($ligne,'Code');

          if($declibre->charger_lien($lien)){
            $nouvel_declibre=false;
          }
          else $declibre->lien=$lien;

          $declibre->ref=$ref;
          $declibre->stock=intval($this->getValeur($ligne,'ST.Qte'));
          $declibre->prix=floatval(str_replace(',','.',$this->getValeur($ligne,'PxVenteTTC0')));

          $declibredesc=new Declibredesc();

          if($nouvel_declibre) {
            $id_declibre=$declibre->add();
            $declibre->id=$id_declibre;
            $declibredesc->declibre=$id_declibre;
          }
          else {
            $id_declibre=$declibre->id;
            $declibre->maj();
            $declibredesc->charger($id_declibre);
          }

          preg_match('#\(+(.*)\)+#',$this->getValeur($ligne,'Designation'),$declibre_texte);
          $declibredesc->declinaison=$this->nettoyerChaine($declibre_texte[1],true,true);
          $declibredesc->lang=1;
          if($nouvel_declibre) $declibredesc->add();
          else $declibredesc->maj();

          //echo "avant:".$produit->stock;

          $declibre->maj_stock($produit);
          $declibre->maj_prix($produit);

          //echo "apres:".$produit->stock;

          //$produit->charger($produit->id);

          //echo "charger:".$produit->stock;
        }

        if(!isset($this->retour['traite'])) $this->retour['traite']=array();
        if(!array_key_exists($produit->id,$this->retour['traite'])) $this->retour['traite'][$produit->id]=array();
        if($declinaison) $this->retour['traite'][$produit->id][]=$id_declibre;

      }

    }

  }

  function nettoyerDeclinaisons(){

    $declibre=new Declibre();
    $produit = new Produit();
    $declilibredesc = new Declibredesc();

    if(is_array($this->retour['traite'])){
      foreach($this->retour['traite'] as $key => $traite_prod){

        $sauf="";
        $produit->charger_id($key);

        foreach($traite_prod as $key => $traite_declibre){

          if($sauf!="") $sauf.=",";
          $sauf.=$traite_declibre;

        }

        if($sauf!="") {
          $query = "delete from $declibredesc->table where declibre in (select id from $declibre->table where ref=\"$ref\" and $declibre->table.id not in ($sauf))";
          $resul = $this->query($query);
          CacheBase::getCache()->reset_cache();
          $query = "delete from $declibre->table where $declibre->table.ref=\"$produit->ref\" and $declibre->table.id not in ($sauf)";
          $resul = $this->query($query);
          CacheBase::getCache()->reset_cache();
        }
        else {
          $query = "delete from $declibredesc->table where declibre in (select id from $declibre->table where ref=\"$ref\")";
          $resul = $this->query($query);
          CacheBase::getCache()->reset_cache();
          $query = "delete from $declibre->table where $declibre->table.ref=\"$produit->ref\"";
          $resul = $this->query($query);
          CacheBase::getCache()->reset_cache();
        }

      }
    }

  }

  function masquerToutProduits(){
    $produit = new Produit();
    $exclusion = new Variable();
    $exclusion->charger("ebp-rubrique-exclusion");
    $exclude="";
    foreach(explode(",",$exclusion->valeur) as $k => $v){
      if(intval($v)!=0)$exclude.=" AND rubrique!=".intval($v);
    }
    $query = "update `$produit->table` set ligne=0 where 1".$exclude.";";
    $resul = $produit->query($query);
    $this->retour['nb_efface']=mysql_affected_rows();
    //$declibre=new Declibre();
    //$declibre->delete_sauf_rubrique($exclusion->valeur);
    //$optlibre=new Optlibre();
    //$optlibre->delete_sauf_rubrique($exclusion->valeur);
    $carac_marque = new Variable();
    $carac_marque->charger("ebp-caracteristique-marque");
    $caracval=new Caracval();
    $query = "delete from $caracval->table where caracteristique=$carac_marque->valeur and produit in (select id from $produit->table where 1".$exclude.")";
    $resul = $this->query($query);
    CacheBase::getCache()->reset_cache();
  }

  function initColonnes($ligne){
    foreach($ligne as $k=>$v){
      switch($v){
        case 'Code':
          $this->champs['Code']=$k;
        break;
        case 'Designation':
          $this->champs['Designation']=$k;
        break;
        case 'Famille.Libelle':
          $this->champs['Famille.Libelle']=$k;
        break;
        case 'CodeCollection':
          $this->champs['CodeCollection']=$k;
        break;
        case 'CodeArtMaster':
          $this->champs['CodeArtMaster']=$k;
        break;
        case 'ST.Qte':
          $this->champs['ST.Qte']=$k;
        break;
        case 'PxVenteTTC0':
          $this->champs['PxVenteTTC0']=$k;
        break;
        case 'TauxTVA':
          $this->champs['TauxTVA']=$k;
        break;
        case 'Link38C7.Description':
          $this->champs['Link38C7.Description']=$k;
        break;
        case 'bWeb':
          $this->champs['bWeb']=$k;
        break;
        /*case 'Gamme1.Libelle':
          $this->champs['Gamme1.Libelle']=$k;
        break;
        case 'Gamme2.Libelle':
          $this->champs['Gamme2.Libelle']=$k;
        break;*/
      }
    }
  }

  function getValeur($ligne,$nom){
    if (array_key_exists($this->champs[$nom],$ligne)) return trim($ligne[$this->champs[$nom]]);
    else return "";
  }

  function nettoyerChaine($chaine,$titre=true,$taille_vetement=false){

    $chaine=utf8_encode($chaine);
    $chaine=trim($chaine);
    $chaine=ucfirst($chaine);
    if($titre) $chaine=mb_convert_case($chaine,MB_CASE_TITLE,'UTF-8');
    if($taille_vetement){
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

  function preparerDesc($texte){

    //$texte=nl2br($texte);
    $texte=$this->nettoyerChaine($texte,false);

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

  function substitutions_mail($texte,$html=true){

    $texte = str_replace("__NB_ARTICLES__", count($this->retour['traite']), $texte);

    $erreurs="Aucune";

    if(count($this->retour['erreur'])>0) {
      $erreurs=count($this->retour['erreur'])."";
      if($html) $erreurs.="<br/>";
      foreach ($this->retour['erreur'] as $k => $v){
      if($html) $erreurs.="<br/>";
      $erreurs.="
".$v;
      }
    }

    $texte = str_replace("__ERREURS__", $erreurs, $texte);

    return $texte;
  }

}

?>
