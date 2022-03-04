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
    if(! est_autorise("acces_contenu")) exit; 

    require_once("liste/contenu.php");
    require_once("liste/dossier.php");

    if(!isset($parent)) $parent=0;
    if(!isset($lang)) $lang=$_SESSION["util"]->lang;
    if(!isset($i)) $i=0;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>
<script src="../lib/jquery/jeditable.js" type="text/javascript"></script>
<script src="../lib/jquery/menu.js" type="text/javascript"></script>

<script type="text/javascript">

    $(document).ready(function() {
        edit();
    });

    function edit(){
        $(".texte_edit").editable("ajax/contenu.php", {
            select : true,
            onblur: "submit",
            cssclass : "ajaxedit",
            width: '100%',
            tooltip   : "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
            placeholder: "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
            indicator : '<img src="gfx/indicator.gif" />'
        });

        $(".classement_edit").editable("ajax/classement.php", {
            select : true,
            onblur: "submit",
            cssclass : "ajaxedit",
            tooltip   : "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
            placeholder: "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
            indicator : '<img src="gfx/indicator.gif" />',
            callback : function(value, settings){
                    tri('ASC','<?php echo($parent); ?>','dossier','classement','');
            }
        });

        $(".contenudos_edit").editable("ajax/classement.php", {
            select : true,
            onblur: "submit",
            cssclass : "ajaxedit",
            tooltip   : "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
            placeholder: "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
            indicator : '<img src="gfx/indicator.gif" />',
            callback : function(value, settings){
                    tri('ASC','<?php echo $parent; ?>','contenudos','classement','');
            }
        });
    }

    function checkvalues(type,id){
        $.ajax({
            url:'ajax/contenu.php',
            data: {
                id: type + '_' + id
            }
        });
    }

    function check(nom,type,parent,ligne){

        if(ligne == 0)
            $('input[id^='+nom+']').removeAttr("checked");
        else
            $('input[id^='+nom+']').attr("checked", "checked");

        $.ajax({
            url:'ajax/contenu.php',
            data: {
                id: type,
                parent: parent,
                ligne: ligne
            }
        });
    }

    function tri(order,ref,type,critere,alpha){
        $.ajax({
            type:"GET",
            url:"ajax/tricontenu.php",
            data : "ref="+ref+"&order="+order+"&type="+type+'&critere='+critere+"&alpha="+alpha,
            success : function(html){
                $("#"+type).html(html);
                edit();
            }
        });
    }

    function supprimer_contenu(id, parent){
        if(confirm("Voulez-vous vraiment supprimer ce contenu ?")) location="contenu_modifier.php?id=" + id + "&action=supprimer&parent=" + parent;
    }

    function supprimer_dossier(id, parent){
        if(confirm("Voulez-vous vraiment supprimer ce dossier et tous ses contenus ?")) location="dossier_modifier.php?id=" + id + "&action=supprimer&parent=" + parent;
    }

</script>
</head>
<body>
<div id="wrapper">
<div id="subwrapper">

<?php
    require_once("../fonctions/divers.php");

    $menu="contenu";
    require_once("entete.php");
?>

<div id="contenu_int">

	<p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a><a href="#" onclick="$('#formulaire').submit()"><img src="gfx/suivant.gif" width="12" height="9" border="0" /></a><a href="listdos.php" class="lien04"><?php echo trad('Gestion_contenu', 'admin'); ?></a>

	<?php
	$parentdesc = new Dossierdesc();
	$parentdesc->charger($parent);
	$parentnom = $parentdesc->titre;

	$res = chemin_dos($parent);
	$tot = count($res)-1;

	while($tot --) {
		?>
		<img src="gfx/suivant.gif" width="12" height="9" border="0" />
		<a href="listdos.php?parent=<?php echo($res[$tot+1]->dossier); ?>" class="lien04"><?php echo($res[$tot+1]->titre); ?></a>
		<?php
	}

	$parentdesc = new Dossierdesc();
	$parentdesc->charger($parent);
	$parentnom = $parentdesc->titre;

	if($parent != ""){
		?>
		<img src="gfx/suivant.gif" width="12" height="9" border="0" />
		<?php
	}
	?>

	<a href="listdos.php?parent=<?php echo($parentdesc->dossier); ?>" class="lien04"><?php echo($parentdesc->titre); ?></a>
	<?php
		if ($parent != 0) {
			?>
			<a class="lien04" href="dossier_modifier.php?id=<?php echo $parent ?>">(<?php echo trad('editer', 'admin'); ?>)</a>
			<?php
		}
	?>
	</p>
	<!-- début de la gestion des dossiers de contenu -->
	<div class="entete_liste">
		<div class="titre"><?php echo trad('LISTE_DOSSIERS_CONTENU', 'admin'); ?> </div><div class="fonction_ajout"><a href="dossier_modifier.php?parent=<?php echo($parent); ?>"><?php if($parent == "") { ?><?php echo trad('AJOUTER_DOSSIER', 'admin'); ?><?php } else {?><?php echo trad('AJOUTER_SOUSDOSSIER', 'admin'); ?><?php } ?></a></div>
	</div>
	<ul id="Nav">
			<li style="height:25px; width:636px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
				<?php echo trad('Titre_dossier', 'admin'); ?>
				<ul class="Menu">
					<li style="width:591px;"><a href="javascript:tri('ASC','<?php echo($parent); ?>','dossier','titre','alpha')"><?php echo trad('Ordre_alpha_croissant', 'admin'); ?></a></li>
					<li style="width:591px;"><a href="javascript:tri('DESC','<?php echo($parent); ?>','dossier','titre','alpha')"><?php echo trad('Ordre_alpha_decroissant', 'admin'); ?></a></li>
				</ul>
			</li>
			<li style="height:25px; width:61px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer;">
				<?php echo trad('En_ligne', 'admin'); ?>
				<ul class="Menu">
					<li><a href="javascript:check('dos_ligne','lignetousdos','<?php echo $parent; ?>',1)"><?php echo trad('Tout_cocher', 'admin'); ?></a></li>
					<li><a href="javascript:check('dos_ligne','lignetousdos','<?php echo $parent; ?>',0)"><?php echo trad('Tout_decocher', 'admin'); ?></a></li>
				</ul>
			</li>
			<li style="height:25px; width:61px; border-left:1px solid #96A8B5;"></li>
			<li style="height:25px; width:41px; border-left:1px solid #96A8B5;"></li>
			<li style="height:25px; width:78px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
				<?php echo trad('Classement', 'admin'); ?>
				<ul class="Menu">
					<li><a href="javascript:tri('ASC','<?php echo $parent; ?>','dossier','classement','')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
					<li><a href="javascript:tri('DESC','<?php echo $parent; ?>','dossier','classement','')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
				</ul>
			</li>
			<li style="height:25px; width:44px; border-left:1px solid #96A8B5;"><?php echo trad('Suppr', 'admin'); ?></li>

	</ul>
	<div id="dossier">
		<?php
		lister_dossiers($parent, 'classement', 'ASC', '');
		?>
	</div>


	<div class="entete_liste" style="margin-top:20px">
		<div class="titre"><?php echo trad('LISTE_CONTENUS', 'admin'); ?></div>
		<div class="fonction_ajout"><a href="contenu_modifier.php?dossier=<?php echo($parent); ?>"><?php echo trad('AJOUTER_CONTENU', 'admin'); ?></a></div>
	</div>
	<ul id="Nav2">
			<li style="height:25px; width:634px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
				<?php echo trad('Titre_contenu', 'admin'); ?>
				<ul class="Menu">
					<li style="width:591px;"><a href="javascript:tri('ASC','<?php echo($parent); ?>','contenudos','titre','alpha')"><?php echo trad('Ordre_alpha_croissant', 'admin'); ?></a></li>
					<li style="width:591px;"><a href="javascript:tri('DESC','<?php echo($parent); ?>','contenudos','titre','alpha')"><?php echo trad('Ordre_alpha_decroissant', 'admin'); ?></a></li>
				</ul>
			</li>
			<li style="height:25px; width:61px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer;">
				<?php echo trad('En_ligne', 'admin'); ?>
				<ul class="Menu">
					<li><a href="javascript:check('cont_ligne','lignetouscont','<?php echo $parent; ?>',1)"><?php echo trad('Tout_cocher', 'admin'); ?></a></li>
					<li><a href="javascript:check('cont_ligne','lignetouscont','<?php echo $parent; ?>',0)"><?php echo trad('Tout_decocher', 'admin'); ?></a></li>
				</ul>
			</li>
			<li style="height:25px; width:61px; border-left:1px solid #96A8B5;"></li>
			<li style="height:25px; width:41px; border-left:1px solid #96A8B5;"></li>
			<li style="height:25px; width:78px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;cursor: pointer; ">
				<?php echo trad('Classement', 'admin'); ?>
				<ul class="Menu">
					<li><a href="javascript:tri('ASC','<?php echo $parent; ?>','contenudos','classement','')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
					<li><a href="javascript:tri('DESC','<?php echo $parent; ?>','contenudos','classement','')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
				</ul>
			</li>
			<li style="height:25px; width:44px; border-left:1px solid #96A8B5;"><?php echo trad('Suppr', 'admin'); ?></li>

	</ul>

	<div id="contenudos" class="bordure_bottom">
		<?php
		lister_contenus($parent, 'classement', 'ASC', '');
		?>
	</div>
</div>

<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>