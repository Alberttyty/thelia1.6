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
    if(!isset($parent)) $parent="";
    if(!isset($page)) $page="";
    if(!isset($id)) $id="";
    if(!isset($ligne)) $ligne="";
    if(! est_autorise("acces_catalogue")) exit;
    require_once("../fonctions/divers.php");

    require_once("liste/contenu_associe.php");
    require_once("liste/caracteristique.php");
    require_once("liste/declinaison.php");

    $images_adm = new ImagesAdmin('rubrique', $id, $lang);
    $documents_adm = new DocumentsAdmin('rubrique', $id, $lang);

    switch($action){
        case 'modclassement' : modclassement($id, $parent, $type); break;
        case 'modifier' : modifier($id, $parent, $lang, $titre, $chapo, $description, $postscriptum, $lien, $ligne, $urlsuiv, $urlreecrite); break;
        case 'ajouter' : ajouter($parent, $lang, $titre, $chapo, $description, $postscriptum, $lien, $ligne); break;
        case 'supprimer' : supprimer($id, $parent);
    }

    $images_adm->action($action);
    $documents_adm->action($action);

    function modclassement($id, $parent, $type){

        if($parent == 0) $parent=0;

        $rub = new Rubrique();
        $rub->charger($id);
        $rub->changer_classement($id, $type);


        redirige("parcourir.php?parent=$parent");

    }

    function modifier($id, $parent, $lang, $titre, $chapo, $description, $postscriptum, $lien, $ligne, $urlsuiv, $urlreecrite){

        $rubrique = new Rubrique();
        $rubriquedesc = new Rubriquedesc();
        $rubrique->charger($id);
        $res = $rubriquedesc->charger($id, $lang);

        if(!$res){
                CacheBase::getCache()->reset_cache();
                $temp = new Rubriquedesc();
                $temp->rubrique=$rubrique->id;
                $temp->lang=$lang;
                $lastid = $temp->add();
                $rubriquedesc = new Rubriquedesc();
                $rubriquedesc->charger_id($lastid);
        }

        $parent_tmp=$rubrique->parent;

        if($parent != $parent_tmp){

            $trouve = 0;

            $test = chemin_rub($parent);
            for($i = 0; $i < count($test); $i++)
                if($test[$i]->rubrique == $id){
                    $trouve = 1;
                    break;
                }
            if(! $trouve){
                $rubrique->parent = $parent;
                $rubrique->classement = $rubrique->prochain_classement();
            }
        }

        $rubrique->lien = $lien;
        $rubriquedesc->titre = $titre;
        $rubriquedesc->chapo = $chapo;
        $rubriquedesc->description = $description;
        $rubriquedesc->postscriptum = $postscriptum;

        if($ligne!="") $rubrique->ligne = 1;
        else $rubrique->ligne = 0;

        $rubrique->maj();
        $rubriquedesc->maj();

        if ($parent_tmp != $parent){
            $queryclass = "select * from $rubrique->table where parent=$parent_tmp order by classement";

            $resclass = $rubrique->query($queryclass);

            if($rubrique->num_rows($resclass)>0){
                $i = 1;
                while($rowclass = $rubrique->fetch_object($resclass)){
                    $rub = new Rubrique();
                    $rub->charger($rowclass->id);
                    $rub->classement = $i;
                    $rub->maj();
                    $i++;
                }
            }
        }

        $rubriquedesc->reecrire($urlreecrite);

        ActionsModules::instance()->appel_module("modrub", $rubrique);

        if($urlsuiv) redirige("parcourir.php?parent=".$rubrique->parent);
        else redirige("" . $_SERVER['PHP_SELF'] . "?id=" . $rubrique->id."&lang=".$lang);
        exit;
    }

    function ajouter($parent, $lang, $titre, $chapo, $description, $postscriptum, $lien, $ligne){

        $rubrique = new Rubrique();
        $rubrique->parent=$parent;
        $rubrique->lien = $lien;

        if($ligne!="") $rubrique->ligne = 1;
        else $rubrique->ligne = 0;

        if($parent == "") $parent=0;

        $lastid = $rubrique->add();

        $rubrique->charger($lastid);

        $rubrique->maj();


        $rubriquedesc = new Rubriquedesc();

        $rubriquedesc->rubrique = $lastid;
        $rubriquedesc->lang = $lang;
        $rubriquedesc->titre = $titre;
        $rubriquedesc->chapo = $chapo;
        $rubriquedesc->description = $description;
        $rubriquedesc->postscriptum = $postscriptum;

        $rubriquedesc->add();

        $caracteristique = new Caracteristique();
        $query = "select * from $caracteristique->table";
        $resul = $caracteristique->query($query);

        $rubcaracteristique = new Rubcaracteristique();

        while($resul && $row = $caracteristique->fetch_object($resul)){

            $rubcaracteristique->rubrique = $lastid;
            $rubcaracteristique->caracteristique = $row->id;
            $rubcaracteristique->add();
        }

        $rubriquedesc->reecrire();

        ActionsModules::instance()->appel_module("ajoutrub", $rubrique);

        redirige("" . $_SERVER['PHP_SELF'] . "?id=" . $lastid);

    }

    function supprimer($id, $parent) {

        $rubrique = new Rubrique($id);
        $rubrique->delete();

        ActionsModules::instance()->appel_module("suprub", $rubrique);

        redirige("parcourir.php?parent=" . $parent);
    }

    $rubrique = new Rubrique();
    $rubriquedesc = new Rubriquedesc();

    if($id){
        $rubrique->charger($id);
        $rubriquedesc->charger($id, $lang);
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");
require_once('js/contenu_associe.php');
require_once('js/caracteristique.php');
require_once('js/declinaison.php');
?>

</head>
<body>

<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="catalogue";
	require_once("entete.php");
?>

<div id="contenu_int">
	<p align="left"><a href="index.php"  class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="parcourir.php" class="lien04"><?php echo trad('Gestion_catalogue', 'admin'); ?> </a>
		<?php
		    $parentdesc = new Rubriquedesc();

			$parentdesc->charger($id);

			$parentnom = $parentdesc->titre;

			$res = chemin_rub($id);
			$tot = count($res)-1;

			if($parent || $id ){
			?>
				<img src="gfx/suivant.gif" width="12" height="9" border="0" />
			<?php
			}

			while($tot --){
			?>
				<a href="parcourir.php?parent=<?php echo($res[$tot+1]->rubrique); ?>" class="lien04"> <?php echo($res[$tot+1]->titre); ?></a>
				<img src="gfx/suivant.gif" width="12" height="9" border="0" />
			<?php
			}

		    $parentdesc = new Rubriquedesc();

			if ($parent) $parentdesc->charger($parent);
			else $parentdesc->charger($id);

			$parentnom = $parentdesc->titre;
		?>
		<a href="parcourir.php?parent=<?php echo($parentdesc->rubrique); ?>" class="lien04"> <?php echo($parentdesc->titre); ?></a>

		<img src="gfx/suivant.gif" width="12" height="9" border="0" /> <?php if( !$id) { ?><?php echo trad('Ajouter', 'admin'); ?><?php } else { ?> <?php echo trad('Modifier', 'admin'); ?> <?php } ?>
	</p>

<!-- Début de la colonne de gauche / bloc de la fiche rubrique -->
 <form action="<?php echo($_SERVER['PHP_SELF']); ?>" id="formulaire" method="post" enctype="multipart/form-data">

	<input type="hidden" name="action" value="<?php if(!$id) { ?>ajouter<?php } else { ?>modifier<?php } ?>" />
	<input type="hidden" name="id" value="<?php echo($id); ?>" />
	<input type="hidden" name="lang" value="<?php echo($lang); ?>" />
	<input type="hidden" name="urlsuiv" id="url" value="0" />


	<div id="bloc_description">

		<!-- bloc entete de la rubrique -->
		<div class="entete">
			<div class="titre"><?php echo trad('DESCRIPTION_G_RUBRIQUE', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="$('#formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>

		<!-- bloc descriptif de la rubrique -->
		<table width="100%" cellpadding="5" cellspacing="0">

			<?php if($id != ""){?>
			    <tr class="claire">
			        <th class="designation"><?php echo trad('Changer_langue', 'admin'); ?></th>
			        <th>
			        <?php
						$langl = new Lang();
						$query = "select * from $langl->table";
						$resul = $langl->query($query);
						while($resul && $row = $langl->fetch_object($resul)){
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
		        <td><input name="titre" id="titre" type="text" class="form_long" value="<?php echo htmlspecialchars($rubriquedesc->titre); ?>"/></td>
		   	</tr>
		   	<tr class="claire">
		        <td class="designation"><?php echo trad('Chapo', 'admin'); ?><br /><span class="note"><?php echo trad('courte_descript_intro', 'admin'); ?></span></td>
		        <td> <textarea name="chapo" id="chapo" cols="40" rows="2" class="form_long"><?php echo($rubriquedesc->chapo); ?></textarea></td>
		   	</tr>
		   	<tr class="fonce">
		        <td class="designation"><?php echo trad('Description', 'admin'); ?><br /><span class="note"><?php echo trad('description_complete', 'admin'); ?></span></td>
		        <td><textarea name="description" id="description" cols="40" rows="2" class="form"><?php echo($rubriquedesc->description); ?></textarea></td>
		   	</tr>
		   	<tr class="claire">
		        <td class="designation"><?php echo trad('PS', 'admin'); ?><br /> <span class="note"><?php echo trad('champs_info_complementaire', 'admin'); ?></span></td>
		        <td>
		        <textarea name="postscriptum" id="postscriptum" cols="40" rows="2" class="form_long"><?php echo($rubriquedesc->postscriptum); ?></textarea></td>
		   	</tr>

		    <?php if($id != ""){ ?>

			    <tr class="fonce">
			      <td class="designation"><?php echo trad('Appartenance', 'admin'); ?><br /> <span class="note"><?php echo trad('deplacer2', 'admin'); ?></span></td>
			      <td style="vertical-align:top;">
			        <select name="parent" id="parent" class="form_long">
			    	 <option value="0">-- <?php echo trad('Racine', 'admin'); ?> --</option>
			         <?php

			        echo arbreOptionRub(0, 1, $id, 0, 1);
					 ?>
			          </select>
			        </td>
			    </tr>
			<?php } else { ?>
				<input type="hidden" name="parent" id="parent" value="<?php echo($parent); ?>" />
			<?php } ?>

			<tr class="claire">
				<td class="designation"><?php echo trad('En_ligne', 'admin'); ?></td>
				<td><input name="ligne" id="ligne" type="checkbox" class="form" <?php if($rubrique->ligne || $id == "" ) { ?> checked="checked" <?php } ?>/></td>
			</tr>
		</table>

 		<?php if($id != ""){ ?>

			<!-- début d'information de la rubrique-->

			<div class="entete">
				<div class="titre" style="cursor:pointer" onclick="$('#pliantinfosrub').show('slow');"><?php echo trad('INFO_RUBRIQUE', 'admin'); ?></div>
				<div class="fonction_valider"><a href="#" onclick="$('#formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
			</div>

			<div class="blocs_pliants_prod" id="pliantinfosrub">
				<ul class="lignesimple">
					<li class="cellule_designation" style="width:128px; padding:5px 0 0 5px; background-image:url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">ID</li>
					<li class="cellule" style="width:450px; padding:5px 0 0 5px; background-image:url(gfx/degrade_ligne1.png); background-repeat: repeat-x;"><?php echo($rubrique->id); ?></li>
				</ul>

				<ul class="lignesimple_haute">
					<li class="cellule_designation" style="width:128px; padding:5px 0 0 5px;"><?php echo trad('Champs_libre', 'admin'); ?></li>
					<li class="cellule" style="width:450px;padding: 5px 0 0 5px;"><input name="lien" id="lien" type="text" class="form_long" value="<?php echo($rubrique->lien); ?>"/></li>
				</ul>

				<ul class="lignesimple_haute">
					<li class="cellule_designation" style="width:128px; padding:5px 0 0 5px;"><?php echo trad('URL_reecrite', 'admin'); ?></li>
					<li class="cellule" style="width:450px;padding: 5px 0 0 5px;"><input type="text" name="urlreecrite" value="<?php echo htmlspecialchars(rewrite_rub("$rubrique->id", $lang)); ?>" class="form_long" /></li>
				</ul>

				<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantinfosrub').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
			</div>
			<!-- fin d'information de la rubrique-->


			<!-- bloc de gestion des contenus associés de la rubrique-->
			<div class="entete">
				<div class="titre" style="cursor:pointer" onclick="$('#pliantcontenuasso').show('slow');"><?php echo trad('GESTION_CONTENUS_ASSOCIES', 'admin'); ?></div>
			</div>

			<div class="blocs_pliants_prod" id="pliantcontenuasso">
				<ul class="ligne1">
							<li class="cellule">
							<select class="form_select" id="contenuassoc_dossier" onchange="charger_listcont(this.value, 0,'<?php echo $rubrique->id; ?>');">
					     	<option value="">&nbsp;</option>
					     	 <?php
			 					echo arbreOption_dos(0, 1, 0, 0, 1);
			 				?>
							</select></li>
							<li class="cellule">
							<select class="form_select" id="select_prodcont"></select>
							</li>
							<li class="cellule"><a href="javascript:contenu_ajouter($('#select_prodcont').val(), 0,'<?php echo $rubrique->id; ?>')"><?php echo trad('AJOUTER', 'admin'); ?></a></li>
				</ul>

				<ul id="contenuassoc_liste">
				<?php
					lister_contenuassoc(0, $rubrique->id);
				?>
				</ul>

				<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantcontenuasso').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
			</div>
			<!-- fin du bloc de gestion des contenus associés de la rubrique-->

			<?php
				$rubcaracteristique = new Rubcaracteristique();
				$query = "select * from $rubcaracteristique->table where rubrique=$rubrique->id";
				$resul = $rubcaracteristique->query($query);
				$listeid = "";
				while($resul && $row = $rubcaracteristique->fetch_object($resul)){
					$listeid .= $row->caracteristique.",";
				}
				if(strlen($listeid) > 0){
					$listeid = substr($listeid,0,strlen($listeid)-1);

					$caracteristique = new Caracteristique();
					$query = "select * from $caracteristique->table where id NOT IN($listeid)";
					$resul = $caracteristique->query($query);
				}
				else{
					$caracteristique = new Caracteristique();
					$query = "select * from $caracteristique->table";
					$resul = $caracteristique->query($query);
				}
			?>

			<!-- début du bloc de gestion des caractéristiques de la rubrique-->
			<div class="entete">
				<div class="titre" style="cursor:pointer" onclick="$('#pliantcaracteristique').show('slow');"><?php echo trad('GESTION_CARACTERISTIQUES_ASSOCIEES', 'admin'); ?></div>
			</div>
			<div class="blocs_pliants_prod" id="pliantcaracteristique">
				<ul class="ligne1">
					<li class="cellule" id="liste_prod_caracteristique">
					<?php caracteristique_liste_select($rubrique->id); ?>
					</li>
					<li class="cellule"><a href="javascript:caracteristique_ajouter($('#prod_caracteristique').val())"><?php echo trad('Ajouter', 'admin'); ?></a></li>
				</ul>

				<ul id="caracteristique_liste">
					<?php lister_caracteristiques_rubrique($rubrique->id) ?>
				</ul>
				<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantcaracteristique').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
			</div>
			<!-- fin du bloc de gestion des caractéristiques de la rubrique-->

			<!-- début du bloc de gestion des déclinaisons de la rubrique-->
			<div class="entete">
				<div class="titre" style="cursor:pointer" onclick="$('#pliantdeclinaisons').show('slow');"><?php echo trad('GESTION_DECLINAISONS_ASSOCIEES', 'admin'); ?></div>
			</div>
			<div class="blocs_pliants_prod" id="pliantdeclinaisons">
				<ul class="ligne1">
					<li class="cellule" id="liste_prod_decli">
					<?php declinaison_liste_select($rubrique->id); ?>
					</li>
					<li class="cellule"><a href="javascript:declinaison_ajouter($('#prod_decli').val())"><?php echo trad('Ajouter', 'admin'); ?></a></li>
				</ul>


				<ul id="declinaison_liste">
					<?php
					lister_declinaisons_rubrique($rubrique->id);
					?>
				</ul>

				<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantdeclinaisons').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
			</div>
			<!-- fin du bloc de gestion des déclinaisons de la rubrique-->


			<div class="patchplugin">
			<?php
				ActionsAdminModules::instance()->inclure_module_admin("rubriquemodifier");
			?>
			</div>

		<?php } ?>
	</div>
	<!-- fin du bloc_description -->
</form>

<?php if($id != "") { ?>

<!-- bloc de gestion des photos et documents / colonne de droite -->
<div id="bloc_photos">
<!-- début du bloc Boite à outils de la rubrique -->
<div class="entete">
	<div class="titre"><?php echo trad('BOITE_OUTILS', 'admin'); ?></div>
</div>
<div class="bloc_transfert">
	<div class="claire">
		<div class="champs" style="padding-top:10px; width:375px;">
			<?php
			$query = "select max(classement) as maxclassement from $rubrique->table where parent=$rubrique->parent";
			$resul = $rubrique->query($query);
			$maxclassement = $rubrique->get_result($resul,0,"maxclassement");

			$query = "select min(classement) as minclassement from $rubrique->table where parent=$rubrique->parent";
			$resul = $rubrique->query($query);
			$minclassement = $rubrique->get_result($resul,0,"minclassement");

			$classement = $rubrique->classement;
			if($classement > $minclassement){
				$prec = $rubrique->classement;

				do{
					$prec--;
					$queryclassement = "select id from $rubrique->table where parent=$rubrique->parent and classement=$prec";
					$resulclassement = $rubrique->query($queryclassement);
				}while(!$rubrique->num_rows($resul) && $prec > $minclassement);

				if($rubrique->num_rows($resulclassement) != 0){
					$idprec = $rubrique->get_result($resulclassement,0,"id");
			?>
			<a href="rubrique_modifier.php?id=<?php echo $idprec; ?>" ><img src="gfx/precedent.png" alt="Rubrique précédente" title="Rubrique précédente" style="padding:0 5px 0 0;margin-top:-5px;height:38px;"/></a>
			<?php
				}
			}
			?>

			<!-- pour visualiser la page rubrique correspondante en ligne -->
			<a title="Voir la rubrique en ligne" href="<?php echo urlfond("rubrique", "id_rubrique=$id", true); ?>" target="_blank" ><img src="gfx/site.png" alt="Voir la rubrique en ligne" title="Voir la rubrique en ligne" /></a>
			<a href="#" onclick="$('#formulaire').submit();"><img src="gfx/valider.png" alt="Enregistrer les modifications" title="Enregistrer les modifications" style="padding:0 5px 0 0;"/></a>
			<a href="#" onclick="$('#url').val('1'); $('#formulaire').submit(); "><img src="gfx/validerfermer.png" alt="Enregistrer les modifications et fermer la fiche" title="Enregistrer les modifications et fermer la fiche" style="padding:0 5px 0 0;"/></a>
			<?php
			if($classement < $maxclassement){
				$suivant = $rubrique->classement;

				do{
					$suivant++;
					$query = "select id from $rubrique->table where parent=$rubrique->parent and classement=$suivant";
					$resul = $rubrique->query($query);
				}while(!$rubrique->num_rows($resul) && $suivant<$maxclassement);

				if($rubrique->num_rows($resul) != 0){
					$idsuiv = $rubrique->get_result($resul,0,"id");

			?>
			<a href="rubrique_modifier.php?id=<?php echo $idsuiv; ?>" ><img src="gfx/suivant.png" alt="Rubrique suivante" title="Rubrique suivante" style="padding:0 5px 0 0;"/></a>
			<?php
				}
			}
			?>

   		</div>
   	</div>
</div>
<!-- fin du bloc Boite à outils de la rubrique-->

<!-- début du bloc de transfert des images de la rubrique-->
<div class="entete" style="margin-top:10px;">
	<div class="titre"><?php echo trad('GESTION_PHOTOS', 'admin'); ?></div>
</div>

<?php $images_adm->bloc_transfert() ?>

<!-- fin du bloc de transfert des images de la rubrique-->

<!-- début du bloc de gestion des photos de la rubrique -->
<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsphotos').show('slow');"><img src="gfx/fleche_accordeon_img_dn.gif" /></div>
<div class="blocs_pliants_photo" id="pliantsphotos">

	<?php $images_adm->bloc_gestion() ?>

	<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsphotos').hide();"><img src="gfx/fleche_accordeon_img_up.gif" /></div>
</div>
<!-- fin du bloc de gestion des photos de la rubrique -->


<!-- début du bloc de gestion des documents de la rubrique -->
<div class="entete" style="margin-top:10px;">
	<div class="titre"><?php echo trad('GESTION_DOCUMENTS', 'admin'); ?></div>
</div>

<?php $documents_adm->bloc_transfert() ?>

<!-- fin du bloc transfert des documents de la rubrique -->
<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsfichier').show('slow');"><img src="gfx/fleche_accordeon_img_dn.gif" /></div>
<div class="blocs_pliants_fichier" id="pliantsfichier">

   <?php $documents_adm->bloc_gestion() ?>

	<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantsfichier').hide();"><img src="gfx/fleche_accordeon_img_up.gif" /></div>
</div>

</div> <!-- fin bloc colonne de droite -->
<?php } ?>
</div> <!-- fin bloc-photos colonne contenu-int -->
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>