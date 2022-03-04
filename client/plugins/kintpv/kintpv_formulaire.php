<?php
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
require_once(realpath(dirname(__FILE__)) . "/Kintpv.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");
autorisation("kintpv");
?>
<div>
    <div class="entete_liste_client">
        <div class="titre">IMPORT DES ARTICLES DEPUIS KINTPV</div>
    </div>
    <?php
    $content_dir = SITE_DIR.'client/plugins/kintpv/tmp/';

    /*Vider le dossier*/
    if(!isset($_POST['fichiertmp'])) {
        if ($handle=opendir($content_dir)) {
            while (false!==($entry=readdir($handle))) {
                if($entry!="."&&$entry!="..") unlink($content_dir.$entry);
            }
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $maxsize = ini_get('post_max_size');
        exit("Le fichier dépasse la taille maxium autorisée. ($maxsize)");
    }

    $tmp_file = $_FILES['fichier']['tmp_name'];
    if(!empty($_FILES['fichier']['tmp_name'])) {

        if(!is_uploaded_file($tmp_file)) exit("Le fichier est introuvable.");

        $name_file = $_FILES['fichier']['name'];
        if(!move_uploaded_file($tmp_file, $content_dir . $name_file)) exit("Impossible de copier le fichier dans $content_dir");

        if(file_exists($content_dir.$name_file)) {
            $ext = pathinfo($content_dir.$name_file,PATHINFO_EXTENSION);
            if($ext=="xml") $_POST['fichiertmp']=$name_file;
            if($ext=="zip") {
                $zip = new ZipArchive;
                $zipres = $zip->open($content_dir.$name_file);
                if ($zipres) {
                    $zip->extractTo($content_dir);
                    $zip->close();
                    unlink($content_dir.$name_file);
                    if ($handle=opendir($content_dir)){
                        while (false!==($entry=readdir($handle))) {
                            $ext = pathinfo($content_dir.$entry,PATHINFO_EXTENSION);
                            if($ext=="xml") $_POST['fichiertmp']=$entry;
                            break;
                        }
                    }
                } else exit("Impossible d'extraire le fichier $name_file dans $content_dir");
            }
        }

    }


if(isset($_POST['fichiertmp'])) {

  $fichiertmp=$_POST['fichiertmp'];
  $KinTPV = new KinTPV();

  if(isset($_POST['retour'])) $KinTPV->retour=unserialize(base64_decode($_POST['retour']));
  if(isset($_POST['champs'])) $KinTPV->champs=unserialize(base64_decode($_POST['champs']));

  $nbArticleTraite = $KinTPV->traiterFichier($content_dir.$fichiertmp);

  if(!empty($nbArticleTraite)) {

      unlink($content_dir.$fichiertmp);

      $msg = new Message();
  		$msgdesc = new Messagedesc();
  		$msg->charger("importkintpv");
  		$msgdesc->charger($msg->id);

      $mail = new Mail();
      $mail->envoyer(
      /*to_name*/Variable::lire('nomsite'),
      /*to_adr*/Variable::lire('kintpv-mail-import'),
      /*from_name*/Variable::lire('nomsite'),
      /*from_adresse*/Variable::lire('emailcontact'),
	    /*sujet*/$KinTPV->substitutions_mail($msgdesc->titre),
	    /*corps_html*/$KinTPV->substitutions_mail($msgdesc->description,true),
      /*corps_texte*/$KinTPV->substitutions_mail($msgdesc->descriptiontext,false)
      );

  }

?>

  <div id="progress">
      <div id="progress_canvas">

          <ul class="retour">
              <li>Nombre d'articles traités avec succès : <strong><?php echo count($KinTPV->retour['traite']); ?></strong></li>
              <li>Erreurs : <strong><?php echo count($KinTPV->retour['erreur']) ?></strong>
                  <?php
                  if(count($KinTPV->retour['erreur'])>0) {
                      echo('<span class="erreur_liste">');
                      foreach (array_reverse($KinTPV->retour['erreur']) as $k => $v) {
                          echo('<span class="erreur">'.$v.'</span>');
                      }
                      echo('</span>');
                  }
                  ?>
              </li>
          </ul>

      </div>
  </div>

  <?php } else { ?>
  <form id="formulaire_import" class="import_export" action="module.php?nom=kintpv" method="post" enctype="multipart/form-data">
      <!--<div><span class="chiffre">2</span></div>-->
      <div>
          <input type="file" name="fichier" size="32" />
          <input type="hidden" name="testfichier" value="oui" />
          <input type="submit" value="OK" />
      </div>
  </form>
  <?php } ?>
</div>
