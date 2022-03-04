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
    if(!isset($page)) $page=0;
    if(! est_autorise("acces_codespromos")) exit; 


    $promo = new Promo();

    if($expiration==0)
            $jour = $mois = $annee = 0;

    switch($action){
            case 'ajouter' : ajouter($code, $type, $valeur, $mini, $actif, $limite, $nombre_limite, $jour, $mois, $annee); break;
            case 'modifier' : modifier($id, $code, $type, $valeur, $mini, $actif, $limite, $nombre_limite, $jour, $mois, $annee); break;
            case 'supprimer' : supprimer($id);
    }

    $nbPromoParPage = 20;

    if($page=="") $page=1;

    $query = "select * from $promo->table WHERE actif='1'";
    $resul = mysql_query($query, $promo->link);
    $num = $resul ? mysql_num_rows($resul) : 0;

    $nbpage = ceil($num/$nbPromoParPage);

    $debut = ($page-1) * $nbPromoParPage;

    if($page>1) $pageprec=$page-1;
    else $pageprec=$page;

    if($page<$nbpage) $pagesuiv=$page+1;
    else $pagesuiv=$page;

    $query1 = "SELECT id, code, type, valeur, mini, utilise, limite, DATE_FORMAT(datefin, '%d-%m-%Y') as datefin FROM $promo->table WHERE actif=1 AND (datefin>=CURDATE() OR datefin='0000-00-00') AND (utilise<limite OR limite=0)";
    $rval = mysql_query($query1);
    $nbValide = $rval ? mysql_num_rows($rval) : 0;

    $query2 = "SELECT id, code, type, valeur, mini, utilise, limite, DATE_FORMAT(datefin, '%d-%m-%Y') as datefin, DATEDIFF(NOW(), datefin) as datediff, actif FROM $promo->table WHERE (actif=1 AND ((datefin<CURDATE() AND datefin NOT LIKE '0000-00-00') OR (utilise>=limite AND limite<>0))) OR actif=0";
    $rval = mysql_query($query2);
    $nbInvalide = $rval ? mysql_num_rows($rval) : 0;
    $nbPageValide = (int)($nbValide/$nbPromoParPage) + 1;
    $echangeur = $nbPromoParPage - $nbValide%$nbPromoParPage;

    $debut2 =  ($page-$nbPageValide) * $nbPromoParPage;
    if($nbValide > ($page-1)*$nbPromoParPage)
    {
        $limite1 = 'LIMIT ' . $debut . ', ' . $nbPromoParPage;
        if($nbValide < $page*$nbPromoParPage)
            $limite2 = 'LIMIT ' . $debut2 . ', ' . $echangeur;
        else
            $limite2 = 'LIMIT 0';
    }
    else
    {
        $debut2 -= $nbPromoParPage-$echangeur;
        $limite1 = 'LIMIT 0';
        $limite2 = 'LIMIT ' . $debut2 . ', ' . $nbPromoParPage;
    }

    function modifier($id, $code, $type, $valeur, $mini, $actif, $limite, $nombre_limite, $jour, $mois, $annee){

        $promo = new Promo();
        $promo->charger_id($id);

        $promo->code = $code;
        $promo->type = $type;
        $promo->actif = $actif;
        $promo->valeur = $valeur;
        $promo->mini = $mini;
        $promo->limite = ($limite==0)?0:$nombre_limite;
        $promo->datefin = $annee . "-" . $mois . "-" . $jour;

        $promo->maj();

        ActionsModules::instance()->appel_module("majpromo", $promo);
    }


    function ajouter( $code, $type, $valeur, $mini, $actif, $limite, $nombre_limite, $jour, $mois, $annee){

        $promo = new Promo();

        $promo->code = $code;
        $promo->type = $type;
        $promo->actif = $actif;
        $promo->valeur = $valeur;
        $promo->mini = $mini;
        $promo->limite = ($limite==0)?0:$nombre_limite;
        $promo->datefin = $annee . "-" . $mois . "-" . $jour;
        $promo->actif = 1;
        $promo->add();

        ActionsModules::instance()->appel_module("ajoutpromo", $promo);

    }

    function supprimer($id){

        $promo = new Promo();
        $promo->charger_id($id);
        $promo->actif = -1;
        $promo->maj();

        ActionsModules::instance()->appel_module("suppromo", $promo);
    }



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>
<script src="../lib/jquery/jeditable.js" type="text/javascript"></script>
<script src="../lib/jquery/menu.js" type="text/javascript"></script>
</head>
<body>
<div id="wrapper">
<div id="subwrapper">
<?php
	$menu="paiement";
	require_once("entete.php");
?>

<div id="contenu_int">

<p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a>&nbsp;<img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04"><?php echo trad('Gestion_codes_promos', 'admin'); ?></a></p>


  <div class="entete_liste">
	<div class="titre"><?php echo trad('LISTE_CODES_PROMOS', 'admin'); ?></div><div class="fonction_ajout"><a href="promo_modifier.php"><?php echo trad('AJOUTER_CODE_PROMO', 'admin'); ?></a></div>
</div>
     <ul id="Nav">
		<li style="height:25px; width:207px; border-left:1px solid #96A8B5;"><?php echo trad('Code', 'admin'); ?></li>
		<li style="height:25px; width:80px; border-left:1px solid #96A8B5;"><?php echo trad('Type', 'admin'); ?></li>
		<li style="height:25px; width:67px; border-left:1px solid #96A8B5;"><?php echo trad('Montant', 'admin'); ?></li>
		<li style="height:25px; width:67px; border-left:1px solid #96A8B5;"><?php echo trad('Achat_mini', 'admin'); ?></li>
		<li style="height:25px; width:67px; border-left:1px solid #96A8B5;"><?php echo trad('Code_actif', 'admin'); ?></li>
		<li style="height:25px; width:87px; border-left:1px solid #96A8B5;">Nb util.</li>
		<li style="height:25px; width:57px; border-left:1px solid #96A8B5;">Limite</li>
		<li style="height:25px; width:157px; border-left:1px solid #96A8B5;"><?php echo trad('Date_expi', 'admin'); ?></li>
		<li style="height:25px; width:57px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:17px; border-left:1px solid #96A8B5;"><?php echo trad('Suppr', 'admin'); ?></li>
	</ul>
  <?php
  	$i=0;

  	$promo = new Promo();

  	$query1 .= " " . $limite1;
  	$resul1 = mysql_query($query1, $promo->link);

  	while($resul1 && $row = mysql_fetch_object($resul1)){
  		if(!($i%2)) $fond="ligne_claire_rub";
  		else $fond="ligne_fonce_rub";
  		$i++;
  ?>
  <ul class="<?php echo($fond); ?>">
	<li style="width:200px;"><span class="texte_noedit"><?php echo($row->code); ?></span></li>
	<li style="width:73px;"><?php if($row->type == Promo::TYPE_SOMME) { ?> <?php echo trad('somme', 'admin'); ?> <?php } else { ?> <?php echo trad('pourcentage', 'admin'); ?> <?php } ?></li>
	<li style="width:60px;"><?php echo($row->valeur); ?><?php if($row->type == Promo::TYPE_SOMME) { ?> € <?php } else { ?> % <?php } ?></li>
	<li style="width:60px;"><?php echo($row->mini); ?> €</li>
	<li style="width:60px;"><?php echo trad('oui', 'admin'); ?></li>
	<li style="width:80px;"><?php echo $row->utilise ?></li>
	<li style="width:50px;"><?php echo ($row->limite==0)?trad('Illimite', 'admin'):$row->limite; ?></li>
	<li style="width:150px;"><?php echo ($row->datefin=='00-00-0000')?trad('N\'expire pas', 'admin'):$row->datefin; ?></li>
	<li style="width:50px;"><a href="promo_modifier.php?id=<?php echo($row->id); ?>"><?php echo trad('editer', 'admin'); ?></a></li>
	<li style="width:40px; text-align:center;"><a href="promo.php?id=<?php echo($row->id); ?>&action=supprimer"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>
<?php
}
	$query2 .= ' ' . $limite2;
  	$resul2 = mysql_query($query2, $promo->link);

  	while($resul2 && $row = mysql_fetch_object($resul2)){
  		if(!($i%2)) $fond="ligne_claire_rub";
  		else $fond="ligne_fonce_rub";
  		$i++;
  ?>
  <ul class="<?php echo($fond); ?>">
	<li style="width:200px;"><span class="texte_noedit"><?php echo($row->code); ?></span></li>
	<li style="width:73px;"><?php if($row->type == Promo::TYPE_SOMME) { ?> <?php echo trad('somme', 'admin'); ?> <?php } else { ?> <?php echo trad('pourcentage', 'admin'); ?> <?php } ?></li>
	<li style="width:60px;"><?php echo($row->valeur); ?><?php if($row->type == Promo::TYPE_SOMME) { ?> € <?php } else { ?> % <?php } ?></li>
	<li style="width:60px;"><?php echo($row->mini); ?> €</li>
	<li style="width:60px;"><?php echo ($row->actif==0)?trad('non', 'admin') . '<img src="gfx/picto-alertes.gif" width="17" height="17" border="0" style="float:right" />':trad('oui', 'admin'); ?></li>
	<li style="width:80px;"><?php echo $row->utilise; if($row->limite!=0 && $row->utilise>=$row->limite){ ?><img src="gfx/picto-alertes.gif" width="17" height="17" border="0" style="float:right" /><?php } ?></li>
	<li style="width:50px;"><?php echo ($row->limite==0)?trad('Illimite', 'admin'):$row->limite; ?></li>
	<li style="width:150px;"><?php echo ($row->datefin=='00-00-0000')?trad('N\'expire pas', 'admin'):$row->datefin; if(!empty($row->datediff) && $row->datediff>0){ ?><img src="gfx/picto-alertes.gif" width="17" height="17" border="0" style="float:right" /><?php } ?></li>
	<li style="width:50px;"><a href="promo_modifier.php?id=<?php echo($row->id); ?>"><?php echo trad('editer', 'admin'); ?></a></li>
	<li style="width:40px; text-align:center;"><a href="promo.php?id=<?php echo($row->id); ?>&action=supprimer"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>
<?php } ?>



   <p id="pages"><a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($pageprec); ?>"><?php echo trad('Page_precedente', 'admin'); ?></a> |

     <?php for($i=0; $i<$nbpage; $i++){ ?>
    	 <?php if($page != $i+1){ ?>
  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($i+1); ?>"><?php echo($i+1); ?></a> |
    	 <?php } else {?>
    		  <span class="selected"><?php echo($i+1); ?></span>
    		|
   		  <?php } ?>
     <?php } ?>

    <a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($pagesuiv); ?>"><?php echo trad('Page_suivante', 'admin'); ?></a>
    </p>

</div>
<?php require_once("pied.php"); ?>
</div>
</div>
</body>
</html>
