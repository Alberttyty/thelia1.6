<?php
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php"); 
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/Ebp.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");
	autorisation("ebp");
?>
<div> 
  
  <div class="entete_liste_client">
  	<div class="titre">EXPORT DES VENTES DEPUIS THÉLIA</div>
  </div>
  
  <div class="import_export">
    <div>
      <span class="chiffre">1</span>
    </div>
    <div>
      
      <?php
      $var = new Variable();
      $var->charger("ebp-dernier-export");
      $dernier_export=$var->valeur;
      $commande=new Commande();
      $query = "select count(id) as nb_commande from $commande->table where date>=\"$dernier_export\" and statut<5";
      $resul = $commande->query($query);
      $row = $commande->fetch_object($resul);
      if($row->nb_commande>0){
      ?>
      <a href="/client/plugins/ebp/export_commandes.php">Exporter les commandes depuis Thélia</a>
      <?php
      }
      else
      {
      ?>
      Aucune commande depuis le dernier export.
      <?php
      }
      echo "<span class=\"dernier_export\">Dernier export le ".date('d/m/y H:m',strtotime($dernier_export))."</span>";
      ?>
    </div>
  </div>
</div> 
<div>
  <div class="entete_liste_client">
  	<div class="titre">IMPORT DES ARTICLES DEPUIS EBP</div>
  </div>
<?php

$content_dir = SITE_DIR.'client/plugins/ebp/tmp/';

/*Vider le dossier*/
if(!isset($_POST['fichiertmp'])){
  if ($handle=opendir($content_dir)){
    while (false!==($entry=readdir($handle))) {
        if($entry!="."&&$entry!="..") unlink($content_dir.$entry);
    }
  }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0){

  $maxsize = ini_get('post_max_size');
  exit("Le fichier dépasse la taille maxium autorisée. ($maxsize)");

}

$tmp_file = $_FILES['fichiercsv']['tmp_name'];
if(!empty($_FILES['fichiercsv']['tmp_name'])){
  
  if(!is_uploaded_file($tmp_file))
  {
  	exit("Le fichier est introuvable.");
  }
  
  $name_file = $_FILES['fichiercsv']['name'];	
  
  if(!move_uploaded_file($tmp_file, $content_dir . $name_file)){
		exit("Impossible de copier le fichier dans $content_dir");
	}
  
  if(file_exists($content_dir.$name_file)) {
    $ext = pathinfo($content_dir.$name_file,PATHINFO_EXTENSION);
    if($ext=="csv"){
      $_POST['fichiertmp']=$name_file;
    }
    if($ext=="zip"){
      $zip = new ZipArchive;
      $zipres = $zip->open($content_dir.$name_file);
      if ($zipres) {
        $zip->extractTo($content_dir);
        $zip->close();
        unlink($content_dir.$name_file);
        if ($handle=opendir($content_dir)){
          while (false!==($entry=readdir($handle))) {
              $ext = pathinfo($content_dir.$entry,PATHINFO_EXTENSION);
              if($ext=="csv") $_POST['fichiertmp']=$entry; 
              break;
          }
        }
      } else {
        exit("Impossible d'extraire le fichier $name_file dans $content_dir");
      }
    }  
  }
  
}


        
if(isset($_POST['fichiertmp'])){
  $fichiertmp=$_POST['fichiertmp'];
  $ebp = new Ebp();    
  if(isset($_POST['debut'])) $debut=$_POST['debut'];
  else $debut=$ebp->debut;
  if(isset($_POST['fin'])) $fin=$_POST['fin'];
  else $fin=$ebp->pas;
  if(isset($_POST['retour'])) $ebp->retour=unserialize(base64_decode($_POST['retour']));
  if(isset($_POST['champs'])) $ebp->champs=unserialize(base64_decode($_POST['champs']));   
  $ebp->traiterFichier($content_dir.$fichiertmp,$debut,$fin);
  $debut=$debut+$ebp->pas;
  if(isset($_POST['termine'])) {
  
    $ebp->nettoyerDeclinaisons();
  
    $debut=0;
    unlink($content_dir.$fichiertmp);
    
    $msg = new Message();
		$msgdesc = new Messagedesc();
		$msg->charger("importebp");
		$msgdesc->charger($msg->id);
    
    $mail = new Mail();
    $mail->envoyer(
      /*to_name*/Variable::lire('nomsite'),
      /*to_adr*/Variable::lire('ebp-mail-import'),
      /*from_name*/Variable::lire('nomsite'),
      /*from_adresse*/Variable::lire('emailcontact'),
	    /*sujet*/$ebp->substitutions_mail($msgdesc->titre),
	    /*corps_html*/$ebp->substitutions_mail($msgdesc->description,true),
      /*corps_texte*/$ebp->substitutions_mail($msgdesc->descriptiontext,false));

  }
  $fin=$fin+$ebp->pas;

?>
  
  <div id="progress">
    <div id="progress_canvas">
    
      <ul class="retour">
        <?php if(isset($_POST['termine'])) {?>
          <li class="termine">Importation terminée !</li>
        <?php } else { ?>
          <li class="loader"><img src="/client/plugins/ebp/images/loader.gif" alt="" /></li>  
        <?php } ?>
        <li>Nombre d'articles traités avec succès : <strong><?php echo count($ebp->retour['traite']); ?></strong></li>
        <li>
          Erreurs : <strong><?php echo count($ebp->retour['erreur']) ?></strong>
          <?php if(count($ebp->retour['erreur'])>0) { ?>
                <span class="erreur_liste">
                <?php
                foreach (array_reverse($ebp->retour['erreur']) as $k => $v){
                ?>
                <span class="erreur"><?php echo $v;?></span>
                <?php
                }
                ?>
                </span>
                <?php
                }
          ?>
        </li>
        <?php if(isset($_POST['termine'])) {?>
          <li class="ok"><a href="module.php?nom=ebp">Retour</a></li>
        <?php } ?>
      </ul>
      
      <form action="module.php" method="post" id="ebp_progress">
        <input type="hidden" name="nom" value="ebp" />     
        <input type="hidden" name="debut" value="<?php echo $debut; ?>" />
        <input type="hidden" name="fin" value="<?php echo $fin; ?>" />
        <input type="hidden" name="fichiertmp" value="<?php echo $fichiertmp; ?>" />
        <input type="hidden" name="retour" value="<?php echo base64_encode(serialize($ebp->retour)); ?>" />
        <input type="hidden" name="champs" value="<?php echo base64_encode(serialize($ebp->champs)); ?>" />
        <input type="submit" value="OK" style="display:none;">
      </form>
      
    </div>
  </div>
  
  <?php
  }
  else {
  ?> 

  <form id="formulaire_import" class="import_export" action="module.php?nom=ebp" method="post" enctype="multipart/form-data">
    <div>
      <span class="chiffre">2</span>
    </div>
    <div>
      <input type="file" name="fichiercsv" size="32" />
      <input type="hidden" name="testfichiercsv" value="oui" />
      <input type="submit" value="OK" />
    </div>
  </form>
  
  <?php
  }
  ?>
  
</div> 
