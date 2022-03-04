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
if(!isset($parent)) $parent=0;
if(!isset($lang)) $lang=$_SESSION["util"]->lang;
if(!isset($tabdisp)) $tabdisp="";

if(! est_autorise("acces_configuration")) exit;


require_once("../fonctions/divers.php");
require_once("../lib/JSON.php");


switch($action){
    case 'modclassement' : modclassement($id, $type); break;
    case 'modifier' : modifier($id, $lang, $titre, $chapo, $description, $tabdisp); break;
    case 'ajouter' : ajouter($lang, $id, $titre, $chapo, $description, $tabdisp, $ajoutrub); break;
    case 'ajdeclidisp' : ajdeclidisp($id, $declidisp, $lang); break;
    case 'majdeclidisp' : majdeclidisp($id, $lang); break;
    case 'supprimer' : supprimer($id, $parent); break;
    case 'suppdeclidisp' : suppdeclidisp($declidisp); break;
    case 'modclassementdeclidisp' : modclassementdeclidisp($id, $declidispdesc, $type, $lang); break;
    case 'setclassementdeclidisp' : setclassementdeclidisp($id, $declidispdesc, $classement, $lang); break;
}

function modclassement($id, $type){
    $dec = new Declinaison();
    $dec->charger($id);
    $dec->changer_classement($id, $type);


    redirige("declinaison.php");
}

function modifier($id, $lang, $titre, $chapo, $description, $tabdisp){

    $json = new Services_JSON();

    if(!$lang) $lang=1;

    $declidisp = new Declidisp();
    $declidispdesc = new Declidispdesc();

    $declinaison = new Declinaison();
    $declinaisondesc = new Declinaisondesc();
    $declinaison->charger($id);
    $res = $declinaisondesc->charger($declinaison->id, $lang);

    if(!$res){
            $temp = new Declinaisondesc();
            $temp->declinaison=$declinaison->id;
            $temp->lang=$lang;
            $temp->add();
            $declinaisondesc->charger($declinaison->id, $lang);

    }


    $declinaisondesc->chapo = $chapo;
    $declinaisondesc->description = $description;
    $declinaisondesc->titre = $titre;

    $declinaisondesc->chapo = str_replace("\n", "<br/>", $declinaisondesc->chapo);
    $declinaisondesc->description = str_replace("\n", "<br/>", $declinaisondesc->description);

    $declinaison->maj();
    $declinaisondesc->maj();

    ActionsModules::instance()->appel_module("moddeclinaison", $declinaison);

    redirige($_SERVER['PHP_SELF'] . "?id=" . $declinaison->id);
}

function ajouter($lang, $id, $titre, $chapo, $description, $tabdisp, $ajoutrub){

    $json = new Services_JSON();
    $tabdisp = stripslashes($tabdisp);
    $tabdisp = $json->decode($tabdisp);

    $declinaison = new Declinaison();
    $declinaison->charger($id);

    if($declinaison->id) return;

    $declinaison = new Declinaison();

    $query = "select max(classement) as maxClassement from $declinaison->table";

    $resul = $declinaison->query($query);
    $maxClassement = $resul ? $declinaison->get_result($resul, 0, "maxClassement") : 0;


    $declinaison->id = $id;
    $declinaison->classement =  $maxClassement + 1;

    $lastid = $declinaison->add();
    $declinaison->id = $lastid;

    $declinaisondesc = new Declinaisondesc();

    $declinaisondesc->chapo = $chapo;
    $declinaisondesc->description = $description;
    $declinaisondesc->declinaison = $lastid;
    $declinaisondesc->lang = $lang;
    $declinaisondesc->titre = $titre;

    $declinaisondesc->chapo = str_replace("\n", "<br/>", $declinaisondesc->chapo);
    $declinaisondesc->description = str_replace("\n", "<br/>", $declinaisondesc->description);

    $declinaisondesc->add();

    $declidisp = new Declidisp();
    $declidispdesc = new Declidispdesc();

    for($i=0; $i<count($tabdisp); $i++){
        $declidisp->declinaison = $lastid;
        $lastidc = $declidisp->add();

        $declidispdesc->declidisp = $lastidc;
        $declidispdesc->lang = $lang;
        $declidispdesc->titre = $tabdisp[$i]->texte;
        $declidispdesc->add();
    }

    if (intval($ajoutrub) == 1)
    {
        $rubrique = new Rubrique();
        $query = "select * from $rubrique->table";
        $resul = $rubrique->query($query);

        while($resul && $row = $rubrique->fetch_object($resul)){
               $rubdeclinaison = new Rubdeclinaison();
               $rubdeclinaison->rubrique = $row->id;
               $rubdeclinaison->declinaison = $lastid;
               $rubdeclinaison->add();
        }
    }

    ActionsModules::instance()->appel_module("ajdeclinaison", $declinaison);

    redirige($_SERVER['PHP_SELF'] . "?id=" . $lastid);

}

function supprimer($id, $parent){

    $declinaison = new Declinaison($id);
    $declinaison->delete();

    ActionsModules::instance()->appel_module("suppdeclinaison", $declinaison);

    redirige("declinaison.php");
}

function suppdeclidisp($declidisp){

    $tdeclidisp = new Declidisp($declidisp);
    $tdeclidisp->delete();

    ActionsModules::instance()->appel_module("suppdeclidisp", $tdeclidisp);
}

function ajdeclidisp($id, $declidisp, $lang){
    $tdeclidisp = new Declidisp();
    $tdeclidisp->declinaison = $id;
    $lastid = $tdeclidisp->add();
    $tdeclidisp->id = $lastid;

    $tdeclidispdesc = new Declidispdesc();
    $tdeclidispdesc->declidisp = $lastid;
    $tdeclidispdesc->lang = $lang;
    $tdeclidispdesc->titre = $declidisp;

    $tdeclidispdesc->classement = 1 + maxClassement($id, $lang);

    $tdeclidispdesc->add();

    ActionsModules::instance()->appel_module("ajdeclidisp", $tdeclidisp);
}

function majdeclidisp($id, $lang){

    global $declidispdesc_titre;

    foreach($declidispdesc_titre as $iddeclidisp => $valeur)
    {
        $declidispdesc = new Declidispdesc();

        $existe = $declidispdesc->charger_declidisp($iddeclidisp, $lang);

        $declidispdesc->declidisp = $iddeclidisp;
        $declidispdesc->lang = $lang;
        $declidispdesc->titre = $valeur;

        if (! $existe)
        {
                $declidispdesc->classement = 1 + maxClassement($id, $lang);

                $declidispdesc->add();
        }
        else
        {
                $declidispdesc->maj();
        }

        $declidisp = new Declidisp($iddeclidisp);
        ActionsModules::instance()->appel_module("moddeclidisp", $declidisp);
    }

}

/* Tri des valeurs de declinaison */

function maxClassement($iddeclinaison, $lang)
{
    $tdeclidispdesc = new Declidispdesc();
    $tdeclidisp = new Declidisp();

    $query = "
            select
                    max(ddd.classement) as maxClassement
            from
                    $tdeclidispdesc->table ddd
            left join
                    $tdeclidisp->table dd on dd.id = ddd.declidisp
            where
                    lang=$lang
            and
                    dd.declinaison=$iddeclinaison
    ";

    $resul = $tdeclidispdesc->query($query);

    return $resul ? intval($tdeclidispdesc->get_result($resul, 0, "maxClassement")) : 0;
}

function modclassementdeclidisp($iddeclinaison, $iddeclidispdesc, $type, $lang)
{
    $declidispdesc = new Declidispdesc();

    if ($declidispdesc->charger($iddeclidispdesc, $lang))
    {
        $remplace = new Declidispdesc();

        if ($type == "M")
        {
            $where = "classement<" . $declidispdesc->classement . " order by classement desc";
        }
        else if ($type == "D")
        {
            $where  = "classement>" . $declidispdesc->classement . " order by classement";
        }

        $declidisp = new Declidisp();

        $query = "
                select
                        *
                from
                        $declidispdesc->table
                where
                        lang=$lang
                and
                        declidisp in (select id from $declidisp->table where declinaison = $iddeclinaison)
                and
                        $where
                limit
                        0, 1
        ";

        if ($remplace->getVars($query))
        {
            $sauv = $remplace->classement;

            $remplace->classement = $declidispdesc->classement;
            $declidispdesc->classement = $sauv;

            $remplace->maj();
            $declidispdesc->maj();
        }
    }
}

function setclassementdeclidisp($iddeclinaison, $iddeclidispdesc, $classement, $lang)
{
    $declidispdesc = new Declidispdesc();

    if ($declidispdesc->charger($iddeclidispdesc, $lang))
    {
        if ($classement == $declidispdesc->classement) return;

        if ($classement > $declidispdesc->classement)
        {
            $offset = -1;
            $between = "$declidispdesc->classement and $classement";
        }
        else
        {
            $offset = 1;
            $between = "$classement and $declidispdesc->classement";
        }

        $declidisp = new Declidisp();

        $query = "
                select
                        id
                from
                        $declidispdesc->table
                where
                        lang=$lang
                and
                        declidisp in (select id from $declidisp->table where declinaison = $iddeclinaison)
                and
                        classement BETWEEN $between
        ";

        $resul = $declidispdesc->query($query);

        $ddd = new Declidispdesc();

        while($resul && $row = $declidispdesc->fetch_object($resul))
        {
            if ($ddd->charger($row->id, $lang))
            {
                $ddd->classement += $offset;
                $ddd->maj();
            }
        }

        $declidispdesc->classement = $classement;
        $declidispdesc->maj();
    }
}

function ecrire_bloc_classement($iddeclinaison, $declidispdesc, $lang, $existe) {

    // On n'affiche rien si l'element n'existe pas encore
    if ($existe) {

        $cour = intval($declidispdesc->classement);
        $haut = $cour - 1;
        $bas = $cour + 1;
        $url = "declinaison_modifier.php?action=modclassementdeclidisp&id=$iddeclinaison&declidispdesc=$declidispdesc->id&lang=$lang&type=";

        $res = '
                <div class="bloc_classement">
                <div class="classement"><a href="'.$url.'M"><img src="gfx/up.gif" border="0" /></a></div>
                <div class="classement"><span id="declidispdesc_'.$declidispdesc->id.'" class="classement_edit">'.$cour.'</span></div>
                <div class="classement"><a href="'.$url.'D"><img src="gfx/dn.gif" border="0" /></a></div>
                </div>
        ';
    }
    else {
        $res = '<div class="bloc_classement"></div>';
    }

    return $res;
}

$declinaison = new Declinaison();
$declinaisondesc = new Declinaisondesc();

$declinaison->charger($id);
$declinaisondesc->charger($declinaison->id, $lang);

$declinaisondesc->chapo = str_replace("<br/>", "\n", $declinaisondesc->chapo);
$declinaisondesc->description = str_replace("<br/>", "\n", $declinaisondesc->description);

$declidisp = new Declidisp();
$declidispdesc = new Declidispdesc();

if(!$lang) $lang=1;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>

<script type="text/javascript" src="../lib/jquery/jeditable.js"></script>
<script type="text/javascript">

	function ajout(){
		  if(document.getElementById('zoneid').value != ""){

		  	 document.getElementById('zoneaction').value='ajdeclidisp';
		  	 document.getElementById('form_modif').submit();
		 }
		 else
			 alert("Veuillez d'abord creer votre declinaison");
	}

	  function maj(){

		  if(document.getElementById('zoneid').value != ""){

	  	 	document.getElementById('zoneaction').value='majdeclidisp';
	  	 	document.getElementById('form_modif').submit();
		 }

		  else alert("Veuillez d'abord creer votre declinaisons");

	  }

	  function suppr(declidisp){
	  	if(confirm("Voulez-vous vraiment supprimer cette entree ?")) location="<?php echo($_SERVER['PHP_SELF'] ); ?>?id=<?php echo($id); ?>&action=suppdeclidisp&declidisp=" + declidisp;

	  }

	  $(document).ready(function() {

			$(".classement_edit").editable(
				function(value, settings) {
					// L'ID est de la forme quelquechose_N, ou N est l'ID du declidispdesc
					// On récupère l'ID uniquement.
					var iddeclidispdesc = $(this).attr('id').replace(/^[^_]+_/, '');

					var loc = "<?php echo($_SERVER['PHP_SELF'] ); ?>?id=<?php echo($id); ?>"
						     + "&action=setclassementdeclidisp"
						     + "&declidispdesc=" + iddeclidispdesc
						     + "&lang=<?php echo $lang ?>"
						     + "&classement="+value;

					location = loc;

				    return value;
				},
				{
			      select : true,
			      onblur: "submit",
			      cssclass : "ajaxedit",
			      indicator : '<img src="gfx/indicator.gif" />',
			      placeholder: "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
			      tooltip   : "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>"
				 }
			  );
	  });

</script>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="configuration";
	require_once("entete.php");
?>

<div id="contenu_int">
   <p align="left"><span class="lien04"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a></span>  <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="declinaison.php" class="lien04"> <?php echo trad('Gestion_declinaisons', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /> <?php if( !$id) { ?><?php echo trad('Ajouter', 'admin'); ?><?php } else { ?> <?php echo $declinaisondesc->titre; ?> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <?php echo trad('Modifier', 'admin'); ?> <?php } ?></p>

	<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" id="form_modif">

		<!-- bloc déclinaisons / colonne gauche -->
		<div id="bloc_description">

			<input type="hidden" name="action" id="zoneaction" value="<?php if(!$id) { ?>ajouter<?php } else { ?>modifier<?php } ?>" />
			<input type="hidden" id="zoneid" name="id" value="<?php echo($id); ?>" />
		 	<input type="hidden" name="lang" value="<?php echo($lang); ?>" />

			<div class="entete_liste_config">
				<div class="titre"><?php echo trad('MODIFICATION_DECLINAISONS', 'admin'); ?></div>
				<div class="fonction_valider"><a href="#" onclick="document.getElementById('form_modif').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
			</div>

	    	<!-- bloc descriptif de la déclinaison -->
			<table width="100%" cellpadding="5" cellspacing="0">
			    <tr class="claire">
			        <th class="designation"><?php echo trad('Changer_langue', 'admin'); ?></th>
			        <th>
			      		<?php
						$langl = new Lang();
						$query = "select * from $langl->table";
						$resul = $langl->query($query);
						while($resul && $row = $langl->fetch_object($resul)) {
							$langl->charger($row->id);
							?>
				 		 	<div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>"><a href="<?php echo($_SERVER['PHP_SELF']); ?>?id=<?php echo($id); ?>&lang=<?php echo($langl->id); ?>"><img src="gfx/lang<?php echo($langl->id); ?>.gif" alt="-" /></a></div>
						<?php } ?>
					</th>
			   	</tr>
			   	<tr class="fonce">
			        <td class="designation"><?php echo trad('Titre_declinaison', 'admin'); ?></td>
			        <td><input name="titre" id="titre" type="text" class="form_long" value="<?php echo htmlspecialchars($declinaisondesc->titre); ?>"/></td>
			   	</tr>
			   	<tr class="claire">
			        <td class="designation"><?php echo trad('Chapo', 'admin'); ?><br /><span class="note"><?php echo trad('courte_descript_intro', 'admin'); ?></span></td>
			        <td> <textarea name="chapo" id="chapo" cols="40" rows="2" class="form_long"><?php echo($declinaisondesc->chapo); ?></textarea></td>
			   	</tr>
			   	<tr class="fonce">
			        <td class="designation"><?php echo trad('Description', 'admin'); ?><br /><span class="note"><?php echo trad('description_complete', 'admin'); ?></span></td>
			        <td><textarea name="description" id="description" cols="53" rows="2" class="form"><?php echo($declinaisondesc->description); ?></textarea></td>
			   	</tr>
			   	<?php if(!$id) { ?>
			   	<tr class="claire">
			        <td class="designation"><?php echo trad('Ajoutauto', 'admin'); ?></td>
			        <td><input type="checkbox" name="ajoutrub" value="1" checked="checked" /><?php echo trad('Ajout_decli_toutes_rubriques', 'admin'); ?></td>
			   	</tr>
			   	<?php } ?>
			</table>

			<div class="patchplugin">
			<?php
				ActionsAdminModules::instance()->inclure_module_admin("declinaisonmodifier");
			?>
			</div>

			<?php if($id != ""){ ?>

				<div class="entete_liste_config">
					<div class="titre"><?php echo trad('INFO_DECLINAISON', 'admin'); ?></div>
				</div>

				<table width="100%" cellpadding="5" cellspacing="0">
				    <tr class="claire">
				    	<th class="designation" style="width:134px;">ID</th>
				        <th><?php echo($declinaison->id); ?></th>
				   	</tr>
				</table>
			<?php } ?>

		</div>
		<!-- fin du bloc de description / colonne de gauche -->

		<?php if($id != ""){ ?>

			<!-- bloc de gestion des valeurs de la déclinaison / colonne de droite-->
			<div id="bloc_colonne_droite">

				<div class="entete_config">
					<div class="titre"><?php echo trad('AJOUTER_VALEUR', 'admin'); ?></div>
				</div>

				<!-- bloc d'ajout des valeurs -->
				<ul class="ligne1">
					<li>
						<input type="hidden" name="id" value="<?php echo($id); ?>" />
	      				<input name="declidisp" type="text" class="form_inputtext" />
					</li>
					<li><a href="#" onclick="ajout()"><?php echo trad('AJOUTER', 'admin'); ?></a></li>
				</ul>

				<div class="entete_config" style="margin:10px 0 0 0;">
					<div class="titre"><?php echo trad('VALEURS_DISPONIBLES', 'admin'); ?></div>

					<div class="maj">
			      		<a href="#" onclick="maj()"><?php echo trad('MAJ', 'admin'); ?></a>
			     	</div>
				</div>

				<!-- bloc des valeurs disponibles -->
		 		<?php

		 			// Les declidispdesc manquants sont placés en fin de classement
	                $query = "
	                	select
	                		dd.*, IFNULL(ddd.classement,".PHP_INT_MAX.") as classmt
	                	from
	                		$declidisp->table dd
	                	left join
	                		$declidispdesc->table ddd on ddd.declidisp = dd.id and lang = $lang
	                	where
	                		dd.declinaison='$id'
	                	order by
	                		classmt, dd.id";

	                $resul = $declidisp->query($query);

	                $i=0;
	                while($resul && $row = $declidisp->fetch_object($resul)) {

	                	$declidispdesc = new Declidispdesc($row->id, $lang);

	                   	if(!($i%2)) $fond="claire";
	  					else $fond="fonce";
	  					$i++;
	            ?>

				<ul class="<?php echo($fond); ?>">
					<li style="width:50px;">ID : <?php echo($row->id); ?></li>
					<li><input type="text" title="<?php echo htmlspecialchars($declidispdesc->titre); ?>" name="declidispdesc_titre[<?php echo($row->id); ?>]" value="<?php echo htmlspecialchars($declidispdesc->titre); ?>" class="form_court" /></li>
					<li style="padding-left: 90px;"><?php echo ecrire_bloc_classement($row->declinaison, $declidispdesc, $lang, $declidispdesc->id != 0) ?></li>
					<li style="text-align:right; width:20px;">
		  			  	<a href="#" onclick="suppr('<?php echo($row->id); ?>')"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a>
					</li>
				</ul>
				<?php } ?>
			</div>
			<!-- fin du bloc colonne de droite -->
		<?php } ?>
    </form>
</div>

<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>