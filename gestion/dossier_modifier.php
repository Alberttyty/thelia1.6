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
require_once("pre.php");
require_once("auth.php");

if(!isset($action)) $action="";
if(!isset($parent)) $parent="0";

if(!isset($lang)) $lang=$_SESSION["util"]->lang;
if(!isset($id)) $id="";
if(!isset($ligne)) $ligne="";

if(! est_autorise("acces_contenu")) exit;

require_once("../fonctions/divers.php");


$images_adm = new ImagesAdmin('dossier', $id, $lang);
$documents_adm = new DocumentsAdmin('dossier', $id, $lang);


switch($action){
    case 'modclassement' : modclassement($id, $parent, $type); break;
    case 'modifier' : modifier($id, $lang, $titre, $chapo, $description, $postscriptum, $ligne, $parent, $urlsuiv, $urlreecrite); break;
    case 'ajouter' : ajouter($parent, $lang, $titre, $chapo, $description, $postscriptum, $ligne); break;
    case 'supprimer' : supprimer($id, $parent);
}

$images_adm->action($action);
$documents_adm->action($action);

function modclassement($id, $parent, $type){

    if($parent == 0) $parent=0;

    $dos = new Dossier();
    $dos->charger($id);
    $dos->changer_classement($id, $type);


    redirige("listdos.php?parent=$parent");
}

function modifier($id, $lang, $titre, $chapo, $description, $postscriptum, $ligne, $parent, $urlsuiv, $urlreecrite){

    $dossier = new Dossier();
    $dossierdesc = new Dossierdesc();
    $dossier->charger($id);
    $res = $dossierdesc->charger($id, $lang);

    if(!$res){
        CacheBase::getCache()->reset_cache();
        $temp = new Dossierdesc();
        $temp->dossier=$dossier->id;
        $temp->lang=$lang;
        $lastid = $temp->add();
        $dossierdesc = new Dossierdesc();
        $dossierdesc->charger_id($lastid);
    }

    $parent_tmp=$dossier->parent;
    if($parent != $parent_tmp){

        $trouve = 0;

        $test = chemin_dos($parent);
        for($i = 0; $i < count($test); $i++)
                if($test[$i]->dossier == $id){
                        $trouve = 1;
                        break;
                }
        if(! $trouve){
                $dossier->parent = $parent;
                $dossier->classement = $dossier->prochain_classement();
        }
    }

    $dossierdesc->titre = $titre;
    $dossierdesc->chapo = $chapo;
    $dossierdesc->description = $description;
    $dossierdesc->postscriptum = $postscriptum;

    if($ligne!="") $dossier->ligne = 1;
    else $dossier->ligne = 0;


    $dossier->maj();
    $dossierdesc->maj();

    if ($parent_tmp != $parent){
        $queryclass = "select * from $dossier->table where parent=$parent_tmp order by classement";

        $resclass = mysql_query($queryclass);

        if(mysql_num_rows($resclass)>0){
            $i = 1;
            while($rowclass = mysql_fetch_object($resclass)){
                $rub = new Dossier();
                $rub->charger($rowclass->id);
                $rub->classement = $i;
                $rub->maj();
                $i++;
            }
        }
    }

    $dossierdesc->reecrire($urlreecrite);

    ActionsModules::instance()->appel_module("moddos", $dossier);

    if($urlsuiv){
        redirige("listdos.php?parent=".$dossier->parent);
    }
    else{
        redirige($_SERVER['PHP_SELF'] . "?id=" . $dossier->id."&lang=".$lang);
    }
}

function ajouter($parent, $lang, $titre, $chapo, $description, $postscriptum, $ligne){

    $dossier = new Dossier();
    $dossier->parent=$parent;

    if($ligne!="") $dossier->ligne = 1;
    else $dossier->ligne = 0;

    if($parent == "") $parent=0;

    $lastid = $dossier->add();

    $dossier->charger($lastid);

    $dossier->maj();


    $dossierdesc = new Dossierdesc();

    $dossierdesc->dossier = $lastid;
    $dossierdesc->lang = $lang;
    $dossierdesc->titre = $titre;
    $dossierdesc->chapo = $chapo;
    $dossierdesc->description = $description;
    $dossierdesc->postscriptum = $postscriptum;

    $dossierdesc->add();

    $dossierdesc->reecrire();

    ActionsModules::instance()->appel_module("ajoutdos", $dossier);

    redirige($_SERVER['PHP_SELF'] . "?id=" . $lastid."&lang=".$lang);
}

function supprimer($id, $parent){

    $dossier = new Dossier($id);
    $dossier->delete();

    ActionsModules::instance()->appel_module("supdos", $dossier);

    redirige("listdos.php?parent=" . $parent);
}

$dossier = new Dossier();
$dossierdesc = new Dossierdesc();

if($id){
    $dossier->charger($id);
    $dossierdesc->charger($id, $lang);
}

$query = "select * from $dossier->table where parent=\"$parent\"";
$resul = mysql_query($query, $dossier->link);
$nbres = mysql_num_rows($resul);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>

</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="contenu";
	require_once("entete.php");
?>
<div id="contenu_int">
  <p><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="listdos.php" class="lien04"><?php echo trad('Gestion_contenu', 'admin'); ?> </a>

    <?php
                    $parentdesc = new Dossierdesc();
					$parentdesc->charger($id);
					$parentnom = $parentdesc->titre;

					$res = chemin_dos($id);
					$tot = count($res)-1;

?>


			<?php
				if($parent || $id){

			?>
					<img src="gfx/suivant.gif" width="12" height="9" border="0" />
			<?php
				}
				while($tot --){
			?> <a href="listdos.php?parent=<?php echo($res[$tot+1]->dossier); ?>" class="lien04"><?php echo($res[$tot+1]->titre); ?></a>  <img src="gfx/suivant.gif" width="12" height="9" border="0" />
            <?php
            	}

            ?>

			<?php
                    $parentdesc = new Dossierdesc();
					if($parent) $parentdesc->charger($parent);
					else $parentdesc->charger($id);
					$parentnom = $parentdesc->titre;

			?>
			 <a href="listdos.php?parent=<?php echo($parentdesc->dossier); ?>" class="lien04"><?php echo($parentdesc->titre); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
            <?php if( !$id) { ?><?php echo trad('Ajouter', 'admin'); ?><?php } else { ?> <?php echo trad('Modifier', 'admin'); ?> <?php } ?> </p>

   <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" id="formulaire" enctype="multipart/form-data">
	<input type="hidden" name="action" value="<?php if(!$id) { ?>ajouter<?php } else { ?>modifier<?php } ?>" />
	<input type="hidden" name="id" value="<?php echo($id); ?>" />
 	<input type="hidden" name="lang" value="<?php echo($lang); ?>" />
	<input type="hidden" name="urlsuiv" id="url" value="0" />
<!-- Bloc description -->
<div id="bloc_description">
	<!-- bloc entete de la rubrique -->
		<div class="entete">
			<div class="titre"><?php echo trad('DESCRIPTION_G_DOSSIER', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>
<!-- bloc descriptif de la rubrique -->
<table width="100%" cellpadding="5" cellspacing="0">
	<?php if($id){ ?>
    <tr class="claire">
        <th class="designation"><?php echo trad('Changer_langue', 'admin'); ?></th>
        <th>
        <?php
			$langl = new Lang();
			$query = "select * from $langl->table";
			$resul = mysql_query($query);
			while($row = mysql_fetch_object($resul)){
			$langl->charger($row->id);
		?>
		<div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>">
			<a href="<?php echo($_SERVER['PHP_SELF']); ?>?id=<?php echo($id); ?>&lang=<?php echo($langl->id); ?>">
				<img src="gfx/lang<?php echo($langl->id); ?>.gif" />
			</a>
		</div>
		<?php } ?>
		</th>
   	</tr>
	<?php } ?>
   	<tr class="fonce">
        <td class="designation"><?php echo trad('Titre', 'admin'); ?></td>
        <td><input name="titre" id="titre" type="text" class="form_long" value="<?php echo htmlspecialchars($dossierdesc->titre); ?>"/></td>
   	</tr>
   	<tr class="claire">
        <td class="designation"><?php echo trad('Chapo', 'admin'); ?><br /><span class="note"><?php echo trad('courte_descript_intro', 'admin'); ?></span></td>
        <td> <textarea name="chapo" id="chapo" cols="40" rows="2" class="form_long"><?php echo($dossierdesc->chapo); ?></textarea></td>
   	</tr>
   	<tr class="fonce">
        <td class="designation">Description<br /><span class="note"><?php echo trad('description_complete', 'admin'); ?></span></td>
        <td><textarea name="description" id="description" cols="40" rows="20" class="form_long"><?php echo($dossierdesc->description); ?></textarea></td>
   	</tr>
   	<tr class="claire">
        <td class="designation"><?php echo trad('PS', 'admin'); ?><br /> <span class="note"><?php echo trad('champs_info_complementaire', 'admin'); ?></span></td>
        <td>
        <textarea name="postscriptum" id="postscriptum" cols="40" rows="2" class="form_long"><?php echo($dossierdesc->postscriptum); ?></textarea></td>
   	</tr>
   <?php
	if($id != ""){
   ?>
   <tr class="claire">
      <td class="designation">Appartenance<br /> <span class="note"><?php echo trad('deplacer', 'admin'); ?></span></td>
      <td style="vertical-align:top;">
        <select name="parent" id="parent" class="form_long">
    	 <option value="0">-- <?php echo trad('Racine', 'admin'); ?> --</option>
         <?php

        echo arbreOption_dos(0, 1, $dossier->parent, $_GET['id'], 1);
		 ?>
          </select>
        </span></td>
    </tr>
  <?php
	} else {

   ?>
	<input type="hidden" name="parent" id="parent" value="<?php echo($parent); ?>" />

  <?php
	}
  ?>
  	 <tr class="fonce">
      <td class="designation"><?php echo trad('En_ligne', 'admin'); ?></td>
      <td>
         <input name="ligne" id="ligne" type="checkbox" class="form" <?php if($dossier->ligne || $id == "" ) { ?> checked="checked" <?php } ?>/>
      </td>
    </tr>
    </table>
<?php if($id != ""){ ?>
<!-- bloc d'informations sur le dossier-->
		<div class="entete">
			<div class="titre" style="cursor:pointer" onclick="$('#pliantsinfos').show('slow');"><?php echo trad('INFO_DOSSIER', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>

<div class="blocs_pliants_prod" id="pliantsinfos">

				<ul class="lignesimple">
					<li class="cellule_designation" style="width:140px; background-image:url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">ID</li>
					<li class="cellule" style="width:438px; padding: 5px 0 0 5px; background-image:url(gfx/degrade_ligne1.png); background-repeat: repeat-x;"><?php echo($dossier->id); ?></li>
				</ul>

			<ul class="lignesimple">
				<li class="cellule_designation" style="width:140px;"><?php echo trad('URL_reecrite', 'admin'); ?></li>
				<li class="cellule"><input type="text" name="urlreecrite" value="<?php echo htmlspecialchars(rewrite_dos("$dossier->id", $lang)); ?>" class="form_long" /></li>
			</ul>

		<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsinfos').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>

</div>
 <?php } ?>

<!-- Fin information dossier -->
<div class="patchplugin">
<?php
	ActionsAdminModules::instance()->inclure_module_admin("dossiermodifier");
?>
</div>

</div><!-- fin du bloc_description -->
</form>

<?php
if($id != ""){
?>
<!-- bloc de gestion des photos et documents / colonne de droite -->
<div id="bloc_photos">
<!-- début du bloc Boite à outils du dossier -->
<div class="entete">
	<div class="titre"><?php echo trad('BOITE_OUTILS', 'admin'); ?></div>
</div>
<div class="bloc_transfert">
	<div class="claire">
		<div class="champs" style="padding-top:10px; width:375px;">
			<?php
			$query = "select max(classement) as maxcount from $dossier->table where parent=$dossier->parent";
			$resul = mysql_query($query);
			$maxclassement = $resul ? mysql_result($resul,0,"maxcount") : 0;

			$query = "select min(classement) as mincount from $dossier->table where parent=$dossier->parent";
			$resul = mysql_query($query);
			$minclassement = $resul ? mysql_result($resul,0,"mincount") : 0;

			$classement = $dossier->classement;
			if($classement > $minclassement){
				$prec = $classement;
				do{
					$prec--;
					$query = "select id from $dossier->table where parent=$dossier->parent and classement=$prec";
					$resul = mysql_query($query);
				}while(!mysql_num_rows($resul) && $prec > $minclassement);

				if(mysql_num_rows($resul) != 0){
					 $idprec = mysql_result($resul,0,"id");
			?>
			<a href="dossier_modifier.php?id=<?php echo $idprec; ?>"><img src="gfx/precedent.png" alt="Dossier précédent" title="Dossier précédent" style="padding:0 5px 0 0;margin-top:-5px;height:38px;"/></a>
			<?php
				}
			}
			?>
			<!-- pour visualiser la page rubrique correspondante en ligne -->
			<a title="Voir le dossier en ligne" href="<?php echo urlfond("dossier", "id_dossier=$dossier->id", true); ?>" target="_blank" ><img src="gfx/site.png" alt="Voir le dossier en ligne" title="Voir le dossier en ligne" /></a>
			<a href="#" onclick="document.getElementById('formulaire').submit();"><img src="gfx/valider.png" alt="Enregistrer les modifications" title="Enregistrer les modifications" style="padding:0 5px 0 0;"/></a>
			<a href="#" onclick="document.getElementById('url').value='1'; document.getElementById('formulaire').submit(); "><img src="gfx/validerfermer.png" alt="Enregistrer les modifications et fermer la fiche" title="Enregistrer les modifications et fermer la fiche" style="padding:0 5px 0 0;"/></a>
			<?php
				if($classement<$maxclassement){
					$suivant = $dossier->classement;
					do{
						$suivant++;
						$query = "select id from $dossier->table where parent=$dossier->parent and classement=$suivant";
						$resul = mysql_query($query);
					}while(!mysql_num_rows($resul) && $suivant<$maxclassement);

					if(mysql_num_rows($resul) != 0){
						$idsuiv = mysql_result($resul,0,"id");

			?>
			<a href="dossier_modifier.php?id=<?php echo $idsuiv; ?>" ><img src="gfx/suivant.png" alt="Dossier suivant" title="Dossier suivant" style="padding:0 5px 0 0;"/></a>
			<?php
					}
				}
			?>
		</div>
   	</div>
</div>
<!-- fin du bloc Boite à outils du dossier-->

<!-- début du bloc de transfert des images du dossier-->
<div class="entete" style="margin-top:10px;">
	<div class="titre"><?php echo trad('GESTION_PHOTOS', 'admin'); ?></div>
</div>

<!-- bloc transfert des images -->

<?php $images_adm->bloc_transfert() ?>

<!-- fin du bloc de transfert des images du dossier-->

<!-- début du bloc de gestion des photos du dossier -->
<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsphotos').show('slow');"><img src="gfx/fleche_accordeon_img_dn.gif" /></div>
	<div class="blocs_pliants_photo" id="pliantsphotos">
		<?php $images_adm->bloc_gestion() ?>
	<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsphotos').hide();"><img src="gfx/fleche_accordeon_img_up.gif" /></div>
</div>
<!-- fin du bloc de gestion des photos du dossier -->

<!-- début du bloc de transfert des documents du dossier -->

<div class="entete" style="margin-top:10px;">
		<div class="titre"><?php echo trad('GESTION_DOCUMENTS', 'admin'); ?></div>
</div>

<?php $documents_adm->bloc_transfert() ?>

<!-- fin du bloc transfert des documents du dossier -->
<!-- début du bloc de gestion des documents du dossier -->
<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsfichier').show('slow');"><img src="gfx/fleche_accordeon_img_dn.gif" /></div>
<div class="blocs_pliants_fichier" id="pliantsfichier">

	<?php $documents_adm->bloc_gestion() ?>

    <div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsfichier').hide();"><img src="gfx/fleche_accordeon_img_up.gif" /></div>
</div>

</div>
<!-- fin bloc photos colonne de droite -->
<?php } ?>
</div>
<?php require_once("pied.php");?>
</div>
</div>

</body>
</html>