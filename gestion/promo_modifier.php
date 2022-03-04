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
?>
<?php if(! est_autorise("acces_codespromos")) exit; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>

<?php
	require_once("../fonctions/divers.php");

	if(!isset($id)) $id="";
	if(!isset($types)) $types="";
	if(!isset($utiliseo)) $utiliseo="";
	if(!isset($illimiteo)) $illimiteo="";

	$utilisen = "";
	$illimiten = "";
	$nbLimite = "";
	$typep = "";

?>


<?php
	$promo = new Promo();
	$promo->charger_id($id);

	if($promo->actif) $utiliseo = "checked=\"checked\"";
	else $utilisen = "checked=\"checked\"";

	if($promo->limite==0)
		$illimiteo = "checked=\"checked\"";
	else
	{
		$nbLimite = $promo->limite;
		$illimiten = "checked=\"checked\"";
	}

	if($promo->type == Promo::TYPE_SOMME) $types = "checked=\"checked\"";
	else $typep = "checked=\"checked\"";

	if($promo->datefin=='0000-00-00' || empty($promo->datefin))
	$illimdate = "checked=\"checked\"";
	else
	{
		$nillimdate = "checked=\"checked\"";
		$jour = substr($promo->datefin, 8, 2);
		$mois = substr($promo->datefin, 5, 2);
		$annee = substr($promo->datefin, 0, 4);
	}

?>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="paiement";
	require_once("entete.php");
?>

<div id="contenu_int">
<p><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a>  <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="promo.php" class="lien04"><?php echo trad('Gestion_codes_promos', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04"><?php if($id) { ?><?php echo trad('Modifier', 'admin'); ?> <?php } else { ?> <?php echo trad('Ajouter', 'admin'); ?> <?php } ?></a>
    </p>

<!-- Début de la colonne de gauche -->
<div id="bloc_description">
<form action="promo.php" id="formulaire" method="post">
<div class="bordure_bottom">
<div class="entete">
			<div class="titre"><?php echo trad('MODIFICATION_CODE_PROMO', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
</div>
<input type="hidden" name="action" value="<?php if($id != "") { ?>modifier<?php } else { ?>ajouter<?php } ?>" />
<input type="hidden" name="id" value="<?php echo($id); ?>" />
  	<ul class="ligne_claire_BlocDescription" style="background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
		<li class="designation" style="width:280px; background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;"><?php echo trad('Code', 'admin'); ?></li>
		<li><input name="code" type="text" class="form" value="<?php echo  htmlspecialchars($promo->code); ?>" size="40" /></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:280px;"><?php echo trad('Type', 'admin'); ?></li>
		<li><input name="type" type="radio" class="form" value="<?php echo Promo::TYPE_SOMME ?>" <?php echo($types); ?> />
<?php echo trad('somme', 'admin'); ?>
<input name="type" type="radio" class="form" value="<?php echo Promo::TYPE_POURCENTAGE ?>" <?php echo($typep); ?> />
<?php echo trad('pourcentage', 'admin'); ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:280px;"><?php echo trad('Montant_code_promo', 'admin'); ?></li>
		<li><input name="valeur" type="text" class="form" value="<?php echo($promo->valeur); ?>" size="10" /></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:280px;"><?php echo trad('Montant_achat_mini', 'admin'); ?></li>
		<li><input name="mini" type="text" class="form" value="<?php echo($promo->mini); ?>" size="10" /></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:280px;"><?php echo trad('Code_actif', 'admin'); ?></li>
		<li><?php echo trad('Oui', 'admin'); ?> <input name="actif" type="radio" class="form" value="1" <?php echo($utiliseo); ?> /> &nbsp; <?php echo trad('Non', 'admin'); ?> <input name="actif" type="radio" class="form" value="0" <?php echo($utilisen); ?> /></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:280px;">Utilisation</li>
		<li>Limitée à <input type="text" name="nombre_limite" onclick="(document.getElementById('radio_limite_1').checked='checked')" value="<?php echo $nbLimite; ?>" style="width:30px" /><input name="limite" id="radio_limite_1" type="radio" class="form" value="1"  <?php echo($illimiten); ?>/> &nbsp; <?php echo trad('Illimite', 'admin'); ?> <input name="limite" type="radio" class="form" value="0"  <?php echo($illimiteo); ?>/></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:280px;"><?php echo trad('Date_expi', 'admin'); ?></li>
		<li>Expire le <input name="jour" type="text" class="form" value="<?php echo($jour); ?>" style="width:15px" onclick="(document.getElementById('radio_expiration_1').checked='checked')" />
       <input name="mois" type="text" class="form" value="<?php echo($mois); ?>" style="width:15px" onclick="(document.getElementById('radio_expiration_1').checked='checked')" />
	   <input name="annee" type="text" class="form" value="<?php echo($annee); ?>" style="width:30px" onclick="(document.getElementById('radio_expiration_1').checked='checked')" /><input type="radio" name="expiration" id="radio_expiration_1" value="1" <?php echo $nillimdate; ?> />
			   &nbsp; N'expire pas
			   <input type="radio" name="expiration" value="0" <?php echo $illimdate; ?> /></li>
	</ul>
</div>

<div class="patchplugin">
<?php
	ActionsAdminModules::instance()->inclure_module_admin("promomodifier");
?>
</div>

</form>


</div>
<!-- fin du bloc description -->

   </div>
   <?php require_once("pied.php");?>
   </div>
   </div>
</body>
</html>
