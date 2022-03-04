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
    if(!isset($lang)) $lang=$_SESSION["util"]->lang;
    if(!isset($ligne)) $ligne="";

    if(! est_autorise("acces_contenu")) exit;
    require_once("../fonctions/divers.php");

    $images_adm = new ImagesAdmin('contenu', $id, $lang);
    $documents_adm = new DocumentsAdmin('contenu', $id, $lang);
    ?>
<?php

	switch($action){
		case 'modclassement' : modclassement($id, $parent, $type); break;
		case 'modifier' : modifier($id, $lang, $dossier, $ligne, $titre, $chapo, $description, $postscriptum, $urlsuiv, $urlreecrite); break;
		case 'ajouter' : ajouter($lang, $dossier, $ligne, $titre, $chapo, $description, $postscriptum); break;
		case 'supprimer' : supprimer($id, $parent); break;
	}

	$images_adm->action($action);
	$documents_adm->action($action);
?>
<?php

	function modclassement($id, $parent, $type){

      	$cont = new Contenu($id);
        $cont->changer_classement($id, $type);

	    redirige("listdos.php?parent=" . $parent);
	}


	function modifier($id, $lang, $dossier, $ligne, $titre, $chapo, $description, $postscriptum, $urlsuiv, $urlreecrite){

	 if(!isset($id)) $id="";

		if(!$lang) $lang=1;

		$contenu = new Contenu();
		$contenudesc = new Contenudesc();
		$contenu->charger($id);
		$res = $contenudesc->charger($contenu->id, $lang);

		if(!$res){
			CacheBase::getCache()->reset_cache();
			$temp = new Contenudesc();
			$temp->contenu=$contenu->id;
			$temp->lang=$lang;
			$lastid = $temp->add();
			$contenudesc = new Contenudesc();
			$contenudesc->charger_id($lastid);
		}

		 $contenu->datemodif = date("Y-m-d H:i:s");

		if($contenu->dossier != $dossier){

			$param_old = Contenudesc::calculer_clef_url_reecrite($contenu->id, $contenu->dossier);
			$param_new = Contenudesc::calculer_clef_url_reecrite($contenu->id, $dossier);

			$reecriture = new Reecriture();

			$query_reec = "select * from $reecriture->table where param='&$param_old' and lang=$lang and actif=1";

			$resul_reec = $reecriture->query($query_reec);
			while($row_reec = $reecriture->fetch_object($resul_reec)){
				$tmpreec = new Reecriture();
				$tmpreec->charger_id($row_reec->id);
				$tmpreec->param = "&$param_new";
				$tmpreec->maj();
			}

			$contenu->dossier = $dossier;
			$contenu->classement = $contenu->prochain_classement();
		}

	 	 if($ligne == "on") $contenu->ligne = 1; else $contenu->ligne = 0;
		 $contenudesc->chapo = $chapo;
		 $contenudesc->description = $description;
		 $contenudesc->postscriptum = $postscriptum;
		 $contenudesc->titre = $titre;

	 	 $contenudesc->chapo = str_replace("\n", "<br/>", $contenudesc->chapo);

		$contenu->maj();
		$contenudesc->maj();

 	  $contenudesc->reecrire($urlreecrite);

		ActionsModules::instance()->appel_module("modcont", $contenu);

		if($urlsuiv) redirige("listdos.php?parent=".$contenu->dossier);
	    else redirige("" . $_SERVER['PHP_SELF'] . "?id=" . $contenu->id . "&dossier=" . $contenu->dossier ."&lang=".$lang);
		exit;
	}

	function ajouter($lang, $dossier, $ligne, $titre, $chapo, $description, $postscriptum){

 	 	if(!isset($id)) $id="";

 	 	$contenu = new Contenu();
	 	$contenu->charger($id);

   	 	if($contenu->id) return;

	 	$contenu = new Contenu();

	 	$contenu->datemodif = date("Y-m-d H:i:s");
	 	$contenu->dossier = $dossier;
	 	if($ligne == "on") $contenu->ligne = 1; else $contenu->ligne = 0;

	 	$lastid = $contenu->add();
		$contenu->id = $lastid;

	 	$contenudesc = new Contenudesc();

	 	$contenudesc->chapo = $chapo;
	 	$contenudesc->description = $description;
	 	$contenudesc->postscriptum = $postscriptum;
	 	$contenudesc->contenu = $lastid;
	 	$contenudesc->lang = $lang;
	 	$contenudesc->titre = $titre;

	 	$contenudesc->chapo = str_replace("\n", "<br/>", $contenudesc->chapo);
     	$contenudesc->postscriptum = str_replace("\n", "<br/>", $contenudesc->postscriptum);

	 	$contenudesc->add();

		$contenudesc->reecrire();

		ActionsModules::instance()->appel_module("ajoutcont", $contenu);

	    redirige($_SERVER['PHP_SELF'] . "?id=" . $lastid . "&dossier=" . $contenu->dossier);
		exit;

	}

	function supprimer($id, $parent){

		$contenu = new Contenu($id);
		$contenu->delete();

		ActionsModules::instance()->appel_module("supcont", $contenu);

	    redirige("listdos.php?parent=" . $parent);
		exit;
	}

?>
<?php

	if(!isset($id)) $id="";

	$contenu = new Contenu();
	$contenudesc = new Contenudesc();

	$contenu->charger($id);
	$contenudesc->charger($contenu->id, $lang);

	$contenudesc->chapo = str_replace("<br/>", "\n", $contenudesc->chapo);
	$contenudesc->postscriptum = str_replace("<br/>", "\n", $contenudesc->postscriptum);

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
  <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="listdos.php" class="lien04"><?php echo trad('Gestion_contenu', 'admin'); ?> </a>

    <?php
    				$cont = new Contenu();
    				$cont->charger($id);

    				$contdesc = new Contenudesc();
    				$contdesc->charger($cont->id);

					$parentnom = $contdesc->titre;

					if($cont->dossier) $res = chemin_dos($cont->dossier);
					else $res = chemin_dos($dossier);

					$tot = count($res)-1;

?>



			<?php
				if($cont->dossier || $dossier){

			?>
				<img src="gfx/suivant.gif" width="12" height="9" border="0" />
			<?php
				}

				while($tot --){
			?><a href="listdos.php?parent=<?php echo($res[$tot+1]->dossier); ?>" class="lien04"><?php echo($res[$tot+1]->titre); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
			<?php
            	}

            ?>

			<?php
                    $parentdesc = new Dossierdesc();
                    if($cont->dossier)
						$parentdesc->charger($cont->dossier);
					else $parentdesc->charger($dossier);

					$parentnom = $parentdesc->titre;


			?>
			<a href="listdos.php?parent=<?php echo($parentdesc->dossier); ?>" class="lien04"><?php echo($parentdesc->titre); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />

			 <?php if( $id) { ?>

			<a href="#" class="lien04"><?php echo($contdesc->titre); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
           <?php echo trad('Modifier', 'admin'); ?><?php } else { ?> <?php echo trad('Ajouter', 'admin'); ?> <?php } ?> </p>

<div id="bloc_description">
 <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" id="formulaire" enctype="multipart/form-data">
	<input type="hidden" name="action" value="<?php if(!$id) { ?>ajouter<?php } else { ?>modifier<?php } ?>" />
	<input type="hidden" name="id" value="<?php echo($id); ?>" />
 	<input type="hidden" name="lang" value="<?php echo($lang); ?>" />
 	<input type="hidden" name="dossier" value="<?php echo($dossier); ?>" />
	<input type="hidden" name="urlsuiv" id="url" value="0" />

<!-- bloc descriptif du produit -->
		<div class="entete">
			<div class="titre"><?php echo trad('DESCRIPTION_G', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>
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

			$ttrad = new Dossierdesc();

			if ( (! $ttrad->charger($contenu->dossier, $row->id)) && ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE)
				continue;

		?>
		<div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>">
		<a href="<?php echo($_SERVER['PHP_SELF']); ?>?id=<?php echo($id); ?>&dossier=<?php echo($dossier); ?>&lang=<?php echo($langl->id); ?>"><img src="gfx/lang<?php echo($langl->id); ?>.gif" /></a></div>
		<?php } ?>
</th>
   	</tr>
	<?php } ?>
   	<tr class="fonce">
        <td class="designation"><?php echo trad('Titre', 'admin'); ?></td>
        <td><input name="titre" id="titre" type="text" class="form_long" value="<?php echo htmlspecialchars($contenudesc->titre); ?>" /></td>
   	</tr>
   	<tr class="claire">
        <td class="designation"><?php echo trad('Chapo', 'admin'); ?><br /> <span class="note"><?php echo trad('courte_descript_intro', 'admin'); ?></span></td>
        <td> <textarea name="chapo" id="chapo" cols="40" rows="2" class="form_long"><?php echo($contenudesc->chapo); ?></textarea></td>
   	</tr>
   	<tr class="fonce">
        <td class="designation">Description<br /> <span class="note"><?php echo trad('description_complete', 'admin'); ?></span></td>
        <td>
        <textarea name="description" id="description" cols="40" rows="20"><?php echo($contenudesc->description); ?></textarea></td>
   	</tr>
   	<tr class="claire">
        <td class="designation">Postscriptum<br /> <span class="note"><?php echo trad('champs_info_complementaire', 'admin'); ?></span></td>
        <td><textarea name="postscriptum" id="postscriptum" cols="40" rows="2" class="form_long"><?php echo($contenudesc->postscriptum); ?></textarea></td>
   	</tr>

   <tr class="fonce">
      <td class="designation"><?php echo trad('Appartenance', 'admin'); ?><br /> <span class="note"><?php echo trad('deplacer', 'admin'); ?></span></td>
      <td style="vertical-align:top;">
        <select name="dossier" id="dossier"  class="form_long">
        	<option value="0"><?php echo trad('A la racine', 'admin'); ?></option>
      		<?php if($id != "") echo arbreOption_dos(0, 1, $contenu->dossier, $_GET['dossier'], -1); else echo arbreOption_dos(0, 1, $dossier,  $_GET['dossier'], -1); ?>
      	</select>
      </td>
    </tr>

	<tr class="claire">
      <td class="designation"><?php echo trad('En_ligne', 'admin'); ?></td>
      <td>    <input type="checkbox" name="ligne" id="ligne" class="form" <?php if($contenu->ligne || $contenu->ligne == "") echo "checked"; ?> /></td>
    </tr>
    </table>
<?php if($id != ""){ ?>

<!-- bloc d'informations sur le contenu-->
		<div class="entete">
			<div class="titre" style="cursor:pointer" onclick="$('#pliantsinfos').show('slow');"><?php echo trad('INFO_CONTENU', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>

<div class="blocs_pliants_prod" id="pliantsinfos">

				<ul class="lignesimple">
					<li class="cellule_designation" style="width:140px; background-image:url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">ID</li>
					<li class="cellule" style="width:438px; padding: 5px 0 0 5px; background-image:url(gfx/degrade_ligne1.png); background-repeat: repeat-x;"><?php echo($contenu->id); ?></li>
				</ul>

			<ul class="lignesimple">
				<li class="cellule_designation" style="width:140px;"><?php echo trad('URL_reecrite', 'admin'); ?></li>
				<li class="cellule"><input type="text" name="urlreecrite" value="<?php echo htmlspecialchars(rewrite_cont("$contenu->id", $lang)); ?>" class="form_long" /></li>
			</ul>

		<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsinfos').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>

</div>
 <?php } ?>
 <?php
	ActionsAdminModules::instance()->inclure_module_admin("contenumodifier");
 ?>
   </form>

</div>
 <?php if($id != ""){ ?>
<!-- bloc de gestion des photos et documents / colonne de droite -->
<div id="bloc_photos">
<!-- début du bloc Boite à outils du contenu -->
<div class="entete">
	<div class="titre"><?php echo trad('BOITE_OUTILS', 'admin'); ?></div>
</div>
<div class="bloc_transfert">
	<div class="claire">
		<div class="champs" style="padding-top:10px; width:375px;">
			<?php
			$query = "select max(classement) as maxcount from $contenu->table where dossier=$contenu->dossier";
			$resul = mysql_query($query);
			$maxclassement = mysql_result($resul,0,"maxcount");

			$query = "select min(classement) as mincount from $contenu->table where dossier=$contenu->dossier";
			$resul = mysql_query($query);
			$minclassement = mysql_result($resul,0,"mincount");

			$classement = $contenu->classement;
			if($classement>$minclassement){
				$prec = $contenu->classement;
				do{
					$prec--;
					$query = "select id from $contenu->table where dossier=$contenu->dossier and classement=$prec";
					$resul = mysql_query($query);
				}while(!mysql_num_rows($resul) && $prec > $minclassement);

				if(mysql_num_rows($resul) != 0){
					$idprec = mysql_result($resul,0,"id");
			?>
			<a href="contenu_modifier.php?id=<?php echo $idprec; ?>&dossier=<?php echo $contenu->dossier; ?>"><img src="gfx/precedent.png" alt="Contenu précédent" title="Contenu précédent" style="padding:0 5px 0 0;margin-top:-5px;height:38px;"/></a>
			<?php
				}
			}
			?>
			<!-- pour visualiser la page contenu correspondante en ligne -->
			<a title="Voir le contenu en ligne" href="<?php echo urlfond("contenu", "id_contenu=$contenu->id", true); ?>" target="_blank" ><img src="gfx/site.png" alt="Voir le contenu en ligne" title="Voir le contenu en ligne" /></a>
			<a href="#" onclick="document.getElementById('formulaire').submit();"><img src="gfx/valider.png" alt="Enregistrer les modifications" title="Enregistrer les modifications" style="padding:0 5px 0 0;"/></a>
			<a href="#" onclick="document.getElementById('url').value='1'; document.getElementById('formulaire').submit();"><img src="gfx/validerfermer.png" alt="Enregistrer les modifications et fermer la fiche" title="Enregistrer les modifications et fermer la fiche" style="padding:0 5px 0 0;"/></a>
			<?php
			if($classement<$maxclassement){
				$suivant = $contenu->classement;
				do{
					$suivant++;
					$query = "select id from $contenu->table where dossier=$contenu->dossier and classement=$suivant";
					$resul = mysql_query($query);
				}while(!mysql_num_rows($resul) && $suivant<$maxclassement);

				if(mysql_num_rows($resul) != 0){
					$idsuiv = mysql_result($resul,0,"id");
			?>
			<a href="contenu_modifier.php?id=<?php echo $idsuiv; ?>&dossier=<?php echo $contenu->dossier; ?>" ><img src="gfx/suivant.png" alt="Contenu suivant" title="Contenu suivant" style="padding:0 5px 0 0;"/></a>
			<?php
				}
			}
			?>
		</div>
   	</div>
</div>
<!-- fin du bloc Boite à outils du contenu-->

<!-- début du bloc de transfert des images du contenu-->
<div class="entete" style="margin-top:10px;">
	<div class="titre"><?php echo trad('GESTION_PHOTOS', 'admin'); ?></div>
</div>

<!-- bloc transfert des images -->
<?php $images_adm->bloc_transfert() ?>
<!-- fin du bloc de transfert des images du contenu-->

<!-- début du bloc de gestion des photos du contenu -->
<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsphotos').show('slow');"><img src="gfx/fleche_accordeon_img_dn.gif" /></div>
<div class="blocs_pliants_photo" id="pliantsphotos">

	<?php $images_adm->bloc_gestion() ?>

	<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsphotos').hide();"><img src="gfx/fleche_accordeon_img_up.gif" /></div>
</div>
<!-- fin du bloc de gestion des photos du contenu -->

<!-- début du bloc de transfert des documents du contenu -->
<div class="entete" style="margin-top:10px;">
		<div class="titre"><?php echo trad('GESTION_DOCUMENTS', 'admin'); ?></div>
</div>

<?php $documents_adm->bloc_transfert() ?>
<!-- fin du bloc transfert des documents du contenu -->

<!-- début du bloc de gestion des documents du contenu -->
<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsfichier').show('slow');"><img src="gfx/fleche_accordeon_img_dn.gif" /></div>
<div class="blocs_pliants_fichier" id="pliantsfichier">

		<?php $documents_adm->bloc_gestion() ?>

       <div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsfichier').hide();"><img src="gfx/fleche_accordeon_img_up.gif" /></div>
</div>
</div> <!-- fin bloc-photos colonne de droite -->
   <?php } ?>
</div>
<?php require_once("pied.php"); ?>
</div>
</div>

</body>
</html>