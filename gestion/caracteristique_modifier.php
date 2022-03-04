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

	if(! est_autorise("acces_configuration")) exit;

	require_once("../fonctions/divers.php");
	require_once("../lib/JSON.php");

	if(!isset($action)) $action="";
	if(!isset($lang)) $lang=$_SESSION["util"]->lang;
	if(!isset($tabdisp)) $tabdisp="";
	if(!isset($affiche)) $affiche="";

	switch($action){
		case 'modclassement' : modclassement($id, $type); break;
		case 'modifier' : modifier($id, $lang, $titre, $chapo, $description, $tabdisp, $affiche); break;
		case 'maj' : maj($id, $lang, $titre, $chapo, $description, $tabdisp, $affiche); break;
		case 'ajouter' : ajouter($lang, $id, $titre, $chapo, $description, $tabdisp, $affiche, $ajoutrub); break;
		case 'ajcaracdisp' : ajcaracdisp($id, $caracdisp, $lang); break;
		case 'majcaracdisp' : majcaracdisp($id, $lang); break;
		case 'supprimer' : supprimer($id); break;
		case 'suppcaracdisp' : suppcaracdisp($caracdisp);
		case 'modclassementcaracdisp' : modclassementcaracdisp($id, $cacacdispdesc, $type, $lang); break;
		case 'setclassementcaracdisp' : setclassementcaracdisp($id, $caracdispdesc, $classement, $lang); break;
	}

	function modclassement($id, $type){

      	$car = new Caracteristique();
        $car->charger($id);
        $car->changer_classement($id, $type);

	    redirige("caracteristique.php");

	}

	function modifier($id, $lang, $titre, $chapo, $description, $tabdisp, $affiche){


		if(!$lang) $lang=1;

		$caracteristique = new Caracteristique();
		$caracteristiquedesc = new Caracteristiquedesc();
		$caracteristique->charger($id);
		$res = $caracteristiquedesc->charger($caracteristique->id, $lang);

		if(!$res){
			$temp = new Caracteristiquedesc();
			$temp->caracteristique=$caracteristique->id;
			$temp->lang=$lang;
			$temp->add();
			$caracteristiquedesc->charger($caracteristique->id, $lang);

		}

		 if($affiche!="") $caracteristique->affiche = 1;
		 else $caracteristique->affiche = 0;

		 $caracteristiquedesc->chapo = $chapo;
		 $caracteristiquedesc->description = $description;
		 $caracteristiquedesc->titre = $titre;

	 	 $caracteristiquedesc->chapo = str_replace("\n", "<br/>", $caracteristiquedesc->chapo);
		 $caracteristiquedesc->description = str_replace("\n", "<br/>", $caracteristiquedesc->description);

		 $caracteristique->maj();
		 $caracteristiquedesc->maj();

		 ActionsModules::instance()->appel_module("modcaracteristique", $caracteristique);

	     redirige($_SERVER['PHP_SELF'] . "?id=$id&lang=$lang");
	}

	function ajouter($lang, $id, $titre, $chapo, $description, $tabdisp, $affiche, $ajoutrub){

	 $caracteristique = new Caracteristique();
	 $caracteristique->charger($id);

   	 if($caracteristique->id) return;

	 $caracteristique = new Caracteristique();

	 $query = "select max(classement) as maxClassement from $caracteristique->table";

	 $resul = $caracteristique->query($query);
     $maxClassement = $resul ? $caracteristique->get_result($resul, 0, "maxClassement") : 0;


	 $caracteristique->id = $id;
	 if($affiche!="") $caracteristique->affiche = 1;
	 else $caracteristique->affiche = 0;

	 $caracteristique->classement =  $maxClassement + 1;

	 $lastid = $caracteristique->add();
	 $caracteristique->id = $lastid;

	 $caracteristiquedesc = new Caracteristiquedesc();

	 $caracteristiquedesc->chapo = $chapo;
	 $caracteristiquedesc->description = $description;
	 $caracteristiquedesc->caracteristique = $lastid;
	 $caracteristiquedesc->lang = $lang;
	 $caracteristiquedesc->titre = $titre;

	 $caracteristiquedesc->chapo = str_replace("\n", "<br/>", $caracteristiquedesc->chapo);
     $caracteristiquedesc->description = str_replace("\n", "<br/>", $caracteristiquedesc->description);

	 $caracteristiquedesc->add();

	if (intval($ajoutrub) == 1)
	{
		 $rubrique = new Rubrique();
		 $query = "select * from $rubrique->table";
		 $resul =$rubrique->query($query);

		 while($resul && $row = $rubrique->fetch_object($resul)){
			$rubcaracteristique = new Rubcaracteristique();
			$rubcaracteristique->rubrique = $row->id;
			$rubcaracteristique->caracteristique = $lastid;
			$rubcaracteristique->add();
		 }
	 }

	 ActionsModules::instance()->appel_module("ajcaracteristique", $caracteristique);

	 redirige($_SERVER['PHP_SELF'] . "?id=$lastid&lang=$lang");

	}

	function supprimer($id){

		$caracteristique = new Caracteristique($id);
		$caracteristique->delete();

	 	ActionsModules::instance()->appel_module("suppcaracteristique", $caracteristique);

	    redirige("caracteristique.php");

	}

	function suppcaracdisp($caracdisp){
        $tcaracdisp = new Caracdisp($caracdisp);
		$tcaracdisp->delete();

		ActionsModules::instance()->appel_module("suppcaracdisp", $tcaracdisp);

	}

	function ajcaracdisp($id, $caracdisp, $lang){

		$tcaracdisp = new Caracdisp();
		$tcaracdisp->caracteristique = $id;
		$lastid = $tcaracdisp->add();
		$tcaracdisp->id = $lastid;

		$tcaracdispdesc = new Caracdispdesc();
		$tcaracdispdesc->caracdisp = $lastid;
		$tcaracdispdesc->lang = $lang;
		$tcaracdispdesc->titre = $caracdisp;

		$tcaracdispdesc->classement = 1 + maxClassement($id, $lang);

		$tcaracdispdesc->add();

	 	ActionsModules::instance()->appel_module("ajcaracdisp", $tcaracdisp);

                redirige('caracteristique_modifier.php?id='.$id);
	}

	function majcaracdisp($id, $lang){

		global $caracdispdesc_titre;

		foreach($caracdispdesc_titre as $idcaracdisp => $valeur)
		{
			$caracdispdesc = new Caracdispdesc();

			$existe = $caracdispdesc->charger_caracdisp($idcaracdisp, $lang);

			$caracdispdesc->caracdisp = $idcaracdisp;
			$caracdispdesc->lang = $lang;
			$caracdispdesc->titre = $valeur;

			if (! $existe)
			{
				$caracdispdesc->classement = 1 + maxClassement($id, $lang);

				$caracdispdesc->add();
			}
			else
			{
				$caracdispdesc->maj();
			}

			$caracdisp = new Caracdisp($idcaracdisp);
			ActionsModules::instance()->appel_module("modcaracdisp", $caracdisp);
		}

                redirige('caracteristique_modifier.php?id='.$id);
	}

	/* Tri des valeurs de Caracdisp */

	function maxClassement($idcaracteristique, $lang)
	{
		$caracdispdesc = new Caracdispdesc();
		$caracdisp = new Caracdisp();

		$query = "
			select
				max(ddd.classement) as maxClassement
			from
				$caracdispdesc->table ddd
			left join
				$caracdisp->table dd on dd.id = ddd.caracdisp
			where
				lang=$lang
			and
				dd.caracteristique=$idcaracteristique
		";

		$resul = $caracdispdesc->query($query);

     	return $resul ? intval($caracdispdesc->get_result($resul, 0, "maxClassement")) : 0;
	}

	function modclassementcaracdisp($idcaracteristique, $idcaracdispdesc, $type, $lang)
	{
		$caracdispdesc = new Caracdispdesc();

		if ($caracdispdesc->charger($idcaracdispdesc, $lang))
		{
			$remplace = new Caracdispdesc();

			if ($type == "M")
			{
				$where = "classement<" . $caracdispdesc->classement . " order by classement desc";
			}
			else if ($type == "D")
			{
				$where  = "classement>" . $caracdispdesc->classement . " order by classement";
			}

			$caracdisp = new Caracdisp();

			$query = "
				select
					*
				from
					$caracdispdesc->table
				where
					lang=$lang
				and
					caracdisp in (select id from $caracdisp->table where caracteristique = $idcaracteristique)
				and
					$where
				limit
					0, 1
			";

			if ($remplace->getVars($query))
			{
				$sauv = $remplace->classement;

				$remplace->classement = $caracdispdesc->classement;
				$caracdispdesc->classement = $sauv;

            	$remplace->maj();
            	$caracdispdesc->maj();
			}
		}
	}

	function setclassementcaracdisp($idcaracteristique, $idcaracdispdesc, $classement, $lang)
	{
		$caracdispdesc = new Caracdispdesc();

		if ($caracdispdesc->charger($idcaracdispdesc, $lang))
		{
			if ($classement == $caracdispdesc->classement) return;

			if ($classement > $caracdispdesc->classement)
			{
				$offset = -1;
				$between = "$caracdispdesc->classement and $classement";
			}
			else
			{
				$offset = 1;
				$between = "$classement and $caracdispdesc->classement";
			}

			$caracdisp = new Caracdisp();

			$query = "
				select
					id
				from
					$caracdispdesc->table
				where
					lang=$lang
				and
					caracdisp in (select id from $caracdisp->table where caracteristique = $idcaracteristique)
				and
					classement BETWEEN $between
			";

			$resul = $caracdispdesc->query($query);

			$ddd = new Caracdispdesc();

			while($resul && $row = $caracdispdesc->fetch_object($resul))
			{
				if ($ddd->charger($row->id, $lang))
				{
					$ddd->classement += $offset;
					$ddd->maj();
				}
			}

			$caracdispdesc->classement = $classement;
			$caracdispdesc->maj();
		}
	}

	function ecrire_bloc_classement($idcaracteristique, $caracdispdesc, $lang, $existe) {

		// On n'affiche rien si l'element n'existe pas encore
		if ($existe) {

			$cour = intval($caracdispdesc->classement);
			$haut = $cour - 1;
			$bas = $cour + 1;
			$url = "caracteristique_modifier.php?action=modclassementcaracdisp&id=$idcaracteristique&cacacdispdesc=$caracdispdesc->id&lang=$lang&type=";

			$res = '
				<div class="bloc_classement">
		    		<div class="classement"><a href="'.$url.'M"><img src="gfx/up.gif" border="0" /></a></div>
		    		<div class="classement"><span id="caracdispdesc_'.$caracdispdesc->id.'" class="classement_edit">'.$cour.'</span></div>
		    		<div class="classement"><a href="'.$url.'D"><img src="gfx/dn.gif" border="0" /></a></div>
		 		</div>
		 	';
		}
		else {
			$res = 	'<div class="bloc_classement"></div>';
		}

		return $res;
	}

	$caracteristique = new Caracteristique();
	$caracteristiquedesc = new Caracteristiquedesc();

	$caracteristique->charger($id);
	$caracteristiquedesc->charger($caracteristique->id, $lang);


	$caracteristiquedesc->chapo = str_replace("<br/>", "\n", $caracteristiquedesc->chapo);
	$caracteristiquedesc->description = str_replace("<br/>", "\n", $caracteristiquedesc->description);

	$caracdisp = new Caracdisp();
	$caracdispdesc = new Caracdispdesc();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>

<script type="text/javascript" src="../lib/jquery/jeditable.js"></script>

<script type="text/javascript">

	  function ajout(){

		  if(document.getElementById('zoneid').value != ""){

	  	 	document.getElementById('zoneaction').value='ajcaracdisp';
	  	 	document.getElementById('form_modif').submit();
		 }
		 else
			 alert("Veuillez d'abord creer votre caracteristique");
	  }

	  function maj(){

		  if(document.getElementById('zoneid').value != ""){

	  	 	document.getElementById('zoneaction').value='majcaracdisp';
	  	 	document.getElementById('form_modif').submit();
		 }

		  else alert("Veuillez d'abord creer votre caracteristique");

	  }

	  function suppr(caracdisp){
	  	if(confirm("Voulez-vous vraiment supprimer cette entree ?")) location="<?php echo($_SERVER['PHP_SELF'] ); ?>?id=<?php echo($id); ?>&action=suppcaracdisp&caracdisp=" + caracdisp;

	  }

	  $(document).ready(function() {

			$(".classement_edit").editable(
				function(value, settings) {
					// L'ID est de la forme quelquechose_N, ou N est l'ID du caracdispdesc
					// On récupère l'ID uniquement.
					var idcaracdispdesc = $(this).attr('id').replace(/^[^_]+_/, '');

					var loc = "<?php echo($_SERVER['PHP_SELF'] ); ?>?id=<?php echo($id); ?>"
						     + "&action=setclassementcaracdisp"
						     + "&caracdispdesc=" + idcaracdispdesc
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

	if(!$lang) $lang=1;
?>

<div id="contenu_int">
   <p><span class="lien04"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a></span> <a href="#" onclick="document.getElementById('form_modif').submit()"><img src="gfx/suivant.gif" width="12" height="9" border="0" /></a><a href="configuration.php" class="lien04"> <?php echo trad('Configuration', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /></a><a href="caracteristique.php" class="lien04"> <?php echo trad('Gestion_caracteristiques', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" />  <?php if( !$id) { ?><?php echo trad('Ajouter', 'admin'); ?><?php } else { ?> <?php echo trad('Modifier', 'admin'); ?> <?php } ?></p>

	 <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" id="form_modif">

		<!-- bloc caractéristiques /colonne gauche -->
		<div id="bloc_description">
			<input type="hidden" name="action" id="zoneaction" value="<?php if(!$id) { ?>ajouter<?php } else { ?>modifier<?php } ?>" />
			<input type="hidden" id="zoneid" name="id" value="<?php echo($id); ?>" />
		 	<input type="hidden" name="lang" value="<?php echo($lang); ?>" />

			<!-- bloc entete des caractéristiques -->
			<div class="flottant">
				<div class="entete_liste_config">
					<div class="titre"><?php echo trad('MODIFICATION_DES_CARACTERISTIQUES', 'admin'); ?></div>
					<div class="fonction_valider"><a href="#" onclick="document.getElementById('form_modif').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
				</div>

				<!-- bloc descriptif de la caractéristique -->
				<table width="100%" cellpadding="5" cellspacing="0">
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
				 		 	<div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>"><a href="<?php echo($_SERVER['PHP_SELF']); ?>?id=<?php echo($id); ?>&lang=<?php echo($langl->id); ?>"><img src="gfx/lang<?php echo($langl->id); ?>.gif" alt="-" /></a></div>
						<?php } ?>
						</th>
				   	</tr>
				   	<tr class="fonce">
				        <td class="designation"><?php echo trad('Titre_caracteristique', 'admin'); ?></td>
				        <td><input name="titre" id="titre" type="text" class="form_long" value="<?php echo htmlspecialchars($caracteristiquedesc->titre); ?>"/></td>
				   	</tr>
				   	<tr class="claire">
				        <td class="designation"><?php echo trad('Chapo', 'admin'); ?><br /><span class="note"><?php echo trad('courte_descript_intro', 'admin'); ?></span></td>
				        <td> <textarea name="chapo" id="chapo" cols="40" rows="2" class="form_long"><?php echo($caracteristiquedesc->chapo); ?></textarea></td>
				   	</tr>
				   	<tr class="fonce">
				        <td class="designation"><?php echo trad('Description', 'admin'); ?><br /><span class="note"><?php echo trad('description_complete', 'admin'); ?></span></td>
				        <td><textarea name="description" id="description" cols="53" rows="2" class="form"><?php echo($caracteristiquedesc->description); ?></textarea></td>
				   	</tr>
					<tr class="claire">
				        <td class="designation"><?php echo trad('Visible', 'admin'); ?></td>
				        <td>
				        <input name="affiche" type="checkbox" class="form" <?php if($caracteristique->affiche || $id == "" ) { ?> checked="checked" <?php } ?>/><span class="note"><?php echo trad('permet', 'admin'); ?></span></td>
				    </tr>
				   	<?php if(!$id) { ?>
				   	<tr class="fonce">
				        <td class="designation"><?php echo trad('Ajoutauto', 'admin'); ?></td>
				        <td><input type="checkbox" name="ajoutrub" value="1" checked="checked" /><?php echo trad('Ajout_carac_toutes_rubriques', 'admin'); ?></td>
				   	</tr>
				   	<?php } ?>
				</table>
			</div>

			<?php
				ActionsAdminModules::instance()->inclure_module_admin("caracteristiquemodifier");
			?>

			<?php if($id != ""){ ?>
				<div class="flottant">

					<div class="entete_liste_config">
						<div class="titre"><?php echo trad('INFORMATIONS_SUR_CARACTERISTIQUE', 'admin'); ?></div>
					</div>

					<table width="100%" cellpadding="5" cellspacing="0">
					    <tr class="claire">
					    	<th class="designation" style="width:134px;">ID</th>
					        <th><?php echo($caracteristique->id); ?></th>
					   	</tr>
					</table>
				</div>
			<?php } ?>
		</div>
		<!-- fin du bloc de description / colonne de gauche -->

		<?php if($id != ""){ ?>

			<!-- bloc de gestion des valeurs de la caractéristique / colonne de droite-->
			<div id="bloc_colonne_droite">

				<div class="entete_config">
					<div class="titre"><?php echo trad('AJOUTER_VALEUR', 'admin'); ?></div>
				</div>

				<!-- bloc d'ajout des valeurs -->
				<ul class="ligne1">
					<li>
						<input type="hidden" name="id" value="<?php echo($id); ?>" />
	      				<input name="caracdisp" type="text" class="form_inputtext" />
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
                $query = "
                	select
                		dd.*,  IFNULL(ddd.classement,".PHP_INT_MAX.") as classmt
                	from
                		$caracdisp->table dd
                	left join
                		$caracdispdesc->table ddd on ddd.caracdisp = dd.id and lang = $lang
                	where
                		dd.caracteristique='$id'
                	order by
                		classmt, dd.id";

                $resul = $caracdisp->query($query);

                $i=0;
                while($resul && $row = $caracdisp->fetch_object($resul)) {

                	$caracdispdesc = new Caracdispdesc($row->id, $lang);

                    if(!($i%2)) $fond="claire";
  					else $fond="fonce";
  					$i++;
	            ?>

				<ul class="<?php echo($fond); ?>">
					<li style="width:50px;">ID : <?php echo($row->id); ?></li>
					<li><input style="width: 210px;" title="<?php echo htmlspecialchars($caracdispdesc->titre); ?>" type="text" name="caracdispdesc_titre[<?php echo($row->id); ?>]" value="<?php echo htmlspecialchars($caracdispdesc->titre); ?>" class="form_court" /></li>
					<li><?php echo ecrire_bloc_classement($row->caracteristique, $caracdispdesc, $lang, $caracdispdesc->id != 0) ?></li>
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