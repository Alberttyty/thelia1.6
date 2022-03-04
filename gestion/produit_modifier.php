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
require_once("../fonctions/divers.php");

if (!isset($action)) $action="";
if (!isset($lang)) $lang=$_SESSION["util"]->lang;
if (!isset($page)) $page="";
if (!isset($id)) $id="";
if (!isset($promo)) $promodio="";
if (!isset($nouveaute)) $nouveaute="";
if (!isset($perso)) $perso=0;
if (!isset($ref)) $ref="";
if (!isset($ligne)) $ligne="";

if (! est_autorise("acces_catalogue")) exit;

require_once("../fonctions/divers.php");

require_once("liste/accessoire.php");
require_once("liste/contenu_associe.php");

$images_adm = new ImagesAdmin('produit', $ref, $lang);
$documents_adm = new DocumentsAdmin('produit', $ref, $lang);

switch($action) {
    case 'modclassement' :
		modclassement($ref, $parent, $type);
		break;

	case 'modifier' :
		modifier($id, $lang, $ref, $prix, $ecotaxe, $promo, $prix2, $rubrique, $nouveaute, $perso, $poids, $stock, $tva, $ligne, $titre, $chapo, $description, $postscriptum, $urlsuiv, $urlreecrite);
		break;

	case 'ajouter' :
		ajouter($lang, $ref, $prix, $ecotaxe, $promo, $prix2, $rubrique, $nouveaute, $perso, $poids, $stock, $tva, $ligne, $titre, $chapo, $description, $postscriptum); 		break;

	case 'supprimer' :
		supprimer($ref, $parent);

	case 'dupliquer' :
		dupliquer($ref,$refn,$rubrique);
		break;
}

$images_adm->action($action);
$documents_adm->action($action);

function dupliquer($ref,$refn,$rubrique) {
    $test = new Produit();

    if(! $test->charger($refn)) {
        $produit = new Produit();

        if($produit->charger($ref)) {

            $newproduit = new Produit();

            $newproduit = $produit;
            $newproduit->id = "";
            $newproduit->ref = $refn;

            $lastid = $newproduit->add();

            $produit->charger($ref);

            $lang = new Lang();
            $query = "select * from $lang->table";
            $result = mysql_query($query);
            $nb = mysql_num_rows($result);

            while($row = mysql_fetch_object($result)) {
                $produitdesc = new Produitdesc();

                if($produitdesc->charger($produit->id, $row->id)) {

                    $newproduitdesc = new Produitdesc();
                    $newproduitdesc = $produitdesc;
                    $newproduitdesc->id = "";
                    $newproduitdesc->produit = $lastid;
                    $newproduitdesc->add();

                }
            }

            $caracval = new Caracval();

            $query = "select * from $caracval->table where produit=$produit->id";
            $resul = mysql_query($query);

            while($row = mysql_fetch_object($resul)) {
                $anciencarac = new Caracval();
                $anciencarac->charger($row->produit,$row->caracteristique);

                $newcarac = new Caracval();
                $newcarac = $anciencarac;
                $newcarac->id = "";
                $newcarac->produit = $lastid;
                $newcarac->add();
            }

            $exdecprod = new Exdecprod();
            $query = "select * from $exdecprod->table where produit=$produit->id";
            $resul = mysql_query($query);

            while($row = mysql_fetch_object($resul)) {
                $oldexdec = new Exdecprod();
                $oldexdec->charger($row->produit,$row->declidisp);

                $newexdec = new Exdecprod();
                $newexdec = $oldexdec;
                $newexdec->id = "";
                $newexdec->produit = $lastid;
                $newexdec->add();
            }

            $stock = new Stock();
            $query = "select * from $stock->table where produit=$produit->id";
            $resul = mysql_query($query);

            while($row = mysql_fetch_object($resul)) {
                $oldstock = new Stock();
                $oldstock->charger($row->declidisp,$row->produit);

                $newstock = new Stock();
                $newstock = $oldstock;
                $newstock->id = "";
                $newstock->produit = $lastid;
                $newstock->add();
            }

            ?>
            <script type="text/javascript">
                alert("Duplication correcte");
                location="produit_modifier.php?rubrique=<?php echo $produit->rubrique; ?>&ref=<?php echo $refn; ?>";
            </script>
        <?php
            } else {
            ?>
            <script type="text/javascript">
                alert("Le produit n'existe pas");
            </script>
        <?php
        }
    }
}

function modclassement($ref, $parent, $type) {
    $prod = new Produit();
    $prod->charger($ref);
    $prod->changer_classement($ref, $type);

    redirige("parcourir.php?parent=" . $parent);
}

function modifier($id, $lang, $ref, $prix, $ecotaxe, $promo, $prix2, $rubrique, $nouveaute, $perso, $poids, $stock, $tva, $ligne, $titre, $chapo, $description, $postscriptum, $urlsuiv, $urlreecrite) {

    $produit = new Produit();
    $produitdesc = new Produitdesc();
    $produit->charger($ref);
    $res = $produitdesc->charger($produit->id, $lang);

    if(!$res) {
        CacheBase::getCache()->reset_cache();
        $temp = new Produitdesc();
        $temp->produit=$produit->id;
        $temp->lang=$lang;
        $lastid = $temp->add();
        $produitdesc = new Produitdesc();
        $produitdesc->charger_id($lastid);
    }

    $prix = str_replace(",", ".", $prix);

    $produit->datemodif = date("Y-m-d H:i:s");
    $produit->prix = $prix;
    $produit->prix2 = $prix2;
    $produit->ecotaxe = $ecotaxe;

    if($produit->rubrique != $rubrique) {

        $param_old = Produitdesc::calculer_clef_url_reecrite($produit->id, $produit->rubrique);
        $param_new = Produitdesc::calculer_clef_url_reecrite($produit->id, $rubrique);

        $reecriture = new Reecriture();

        $query_reec = "select * from $reecriture->table where param='&$param_old' and lang=$lang and actif=1";
        $resul_reec = $reecriture->query($query_reec);

        while($row_reec = $reecriture->fetch_object($resul_reec)) {

            $tmpreec = new Reecriture();
            $tmpreec->charger_id($row_reec->id);
            $tmpreec->param = "&$param_new";
            $tmpreec->maj();
        }

        $produit->rubrique = $rubrique;
        $produit->classement =  $produit->prochain_classement();
    }

    if($promo == "on") $produit->promo = 1; else $produit->promo = 0;
    if($nouveaute == "on") $produit->nouveaute = 1; else $produit->nouveaute = 0;
    if($ligne == "on") $produit->ligne = 1; else $produit->ligne = 0;

    $produit->perso = $perso;
    $produit->poids = $poids;
    $produit->stock = $stock;
    $produit->tva = str_replace(",", ".", $tva);

    $produitdesc->chapo = $chapo;
    $produitdesc->description = $description;
    $produitdesc->postscriptum = $postscriptum;
    $produitdesc->titre = $titre;

    $produitdesc->chapo = str_replace("\n", "<br />", $produitdesc->chapo);

    $rubcaracteristique = new Rubcaracteristique();
    $caracteristiquedesc = new Caracteristiquedesc();
    $caracval = new Caracval();

    $query = "SELECT * FROM $rubcaracteristique->table WHERE rubrique='" . $produit->rubrique . "'";
    $resul = mysql_query($query);

    while($row = mysql_fetch_object($resul)) {
            $caracval = new Caracval();
            $deb="caract";
            $deb2="typecaract";

            $val=$row->caracteristique;
            $var = $deb.$val;
            $var2 = $deb2.$val;

            global $$var;
            global $$var2;

            $query2 = "delete from $caracval->table where produit='" . $produit->id . "' and caracteristique='" . $row->caracteristique . "'";
            $resul2 = mysql_query($query2);



            if($$var2 == "c" && $$var != "")
                foreach($$var as $selectval) {
                    if($selectval != ""){
                        $caracval->produit = $produit->id;
                        $caracval->caracteristique = $row->caracteristique;
                        $caracval->caracdisp = $selectval;
                        $caracval->add();
                    }
                }

            else if($$var != "") {
                $caracval->produit = $produit->id;
                $caracval->caracteristique = $row->caracteristique;
                $caracval->valeur = $$var;
                $caracval->add();
            }
        }

        $produit->maj();
        $produitdesc->maj();

        $produitdesc->reecrire($urlreecrite);

        $rubdeclinaison = new Rubdeclinaison();
        $declinaisondesc = new Declinaisondesc();
        $declidisp = new Declidisp();
        $declidispdesc = new Declidispdesc();

        $query = 'SELECT * FROM '.$rubdeclinaison->table.' WHERE rubrique = "'.$rubrique.'"';
        $resul = mysql_query($query);

        $nb = 0;

        while($row = mysql_fetch_object($resul)) {

            $declinaisondesc->charger($row->declinaison);

            $query2 = 'SELECT * FROM '.$declidisp->table.' WHERE declinaison = '.$row->declinaison;
            $resul2 = mysql_query($query2);
            $nbres = mysql_num_rows($resul2);

            while($row2 = mysql_fetch_object($resul2)) {
                $var="stock" . $row2->id;
                $var2="surplus" . $row2->id;
                global $$var, $$var2;

                $stock = new Stock();

                if ($stock->charger($row2->id,$produit->id) == 0) {
                    $stock->declidisp=$row2->id;
                    $stock->produit=$produit->id;
                    $stock->valeur=$$var;
                    $stock->surplus=$$var2;
                    $stock->add();
                    $nb += $stock->valeur;
                } else {
                    $stock->valeur=$$var;
                    $stock->surplus=$$var2;
                    $stock->maj();
                	$nb += $stock->valeur;
                }
            }
        }

        if($nb) $produit->stock = $nb;

        $produit->maj();

        ActionsModules::instance()->appel_module("modprod", $produit);

        if($urlsuiv) {
            ?>
            <script type="text/javascript">
                    window.location="parcourir.php?parent=<?php echo $produit->rubrique; ?>";
            </script>
            <?php
        } else {
            ?>
            <script type="text/javascript">
                    window.location="<?php echo $_SERVER['PHP_SELF']; ?>?ref=<?php echo $produit->ref; ?>&rubrique=<?php echo  $produit->rubrique?>&lang=<?php echo $lang; ?>";
            </script>
            <?php
        }

    }

    function ajouter($lang, $ref, $prix, $ecotaxe, $promo, $prix2, $rubrique, $nouveaute, $perso, $poids, $stock, $tva, $ligne, $titre, $chapo, $description, $postscriptum) {

        $ref = str_replace(" ", "", $ref);
        $ref = str_replace("/", "", $ref);
        $ref = str_replace("+", "", $ref);
        $ref = str_replace(".", "-", $ref);
        $ref = str_replace(",", "-", $ref);
        $ref = str_replace(";", "-", $ref);
        $ref = str_replace("'", "", $ref);
        $ref = str_replace("\n", "", $ref);
        $ref = str_replace("\"", "", $ref);

        $produit = new Produit();
        $produit->charger($ref);

        if($produit->id) {
        	redirige("produit_modifier.php?rubrique=$rubrique&existe=1");
        }

        $produit = new Produit();

        $prix = str_replace(",", ".", $prix);

        $produit->ref = $ref;
        $produit->datemodif = date("Y-m-d H:i:s");
        $produit->prix = $prix;
        $produit->prix2 = $prix2;
        if($produit->prix2 == "") $produit->prix2 = $prix;
        $produit->ecotaxe = $ecotaxe;
        $produit->rubrique = $rubrique;
        if($promo == "on") $produit->promo = 1; else $produit->promo = 0;
        if($nouveaute == "on") $produit->nouveaute = 1; else $produit->nouveaute = 0;
        if($ligne == "on") $produit->ligne = 1; else $produit->ligne = 0;
        $produit->perso = $perso;
        $produit->poids = $poids;
        $produit->stock = $stock;
        $produit->tva = str_replace(",", ".", $tva);

        $lastid = $produit->add();
        $produit->id = $lastid;

        $produitdesc = new Produitdesc();

        $produitdesc->chapo = $chapo;
        $produitdesc->description = $description;
        $produitdesc->postscriptum = $postscriptum;
        $produitdesc->produit = $lastid;
        $produitdesc->lang = $lang;
        $produitdesc->titre = $titre;

        $produitdesc->chapo = str_replace("\n", "<br />", $produitdesc->chapo);

        $produitdesc->add();


        $rubcaracteristique = new Rubcaracteristique();
        $caracteristiquedesc = new Caracteristiquedesc();
        $caracval = new Caracval();


        $query = "select * from $rubcaracteristique->table where rubrique='" . $produit->rubrique . "'";
        $resul = mysql_query($query);

        while($row = mysql_fetch_object($resul)){
            $caracval = new Caracval();
            $deb="caract";
            $deb2="typecaract";

            $val=$row->caracteristique;
            $var = $deb.$val;
            $var2 = $deb2.$val;

            global $$var;
            global $$var2;

            $query2 = "delete from $caracval->table where produit='" . $produit->id . "' and caracteristique='" . $row->caracteristique . "'";
            $resul2 = mysql_query($query2);

            if($$var != "")

            if($$var2 == "c")
                foreach($$var as $selectval) {

                    $caracval->produit = $lastid;
                    $caracval->caracteristique = $row->caracteristique;
                    $caracval->caracdisp = $selectval;
                    $caracval->add();
                }

            else {
                $caracval->produit = $lastid;
                $caracval->caracteristique = $row->caracteristique;
                $caracval->valeur = $$var;
                $caracval->add();
            }
        }


	$rubdeclinaison = new Rubdeclinaison();
   	$declinaisondesc = new Declinaisondesc();
   	$declidisp = new Declidisp();
        $declidispdesc = new Declidispdesc();

   	$query = "select * from $rubdeclinaison->table where rubrique='" . $rubrique . "'";
   	$resul = mysql_query($query);


   	while($row = mysql_fetch_object($resul)){

            $declinaisondesc->charger($row->declinaison);


            $query2 = "select * from $declidisp->table where declinaison='$row->declinaison'";
            $resul2 = mysql_query($query2);
            $nbres = mysql_num_rows($resul2);

            while($row2 = mysql_fetch_object($resul2)){
                $stock = new Stock();
                $stock->declidisp=$row2->id;
                $stock ->produit=$lastid;
                $stock->valeur=0;
                $stock->surplus=0;
                $stock->add();
            }

	}

	$produitdesc->reecrire();

	ActionsModules::instance()->appel_module("ajoutprod", $produit);

        redirige($_SERVER['PHP_SELF'] . "?ref=" . $produit->ref . "&rubrique=" . $produit->rubrique."&lang=".$lang);

    }

    function supprimer($ref, $parent){

        $produit = new Produit($ref);
        $produit->delete();

        ActionsModules::instance()->appel_module("supprod", $produit);

        redirige("parcourir.php?parent=".$parent);
    }



    $produit = new Produit();
    $produitdesc = new Produitdesc();

    $produit->charger($ref);
    $produitdesc->charger($produit->id, $lang);

    $produitdesc->chapo = str_replace("<br />", "\n", $produitdesc->chapo);

    if($produit->tva == ""){
        $tva = Variable::lire("tva");
    }
    else $tva=$produit->tva;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>

<script type="text/javascript">
<!--
	function envoyer(){
	        var ref=$('#ref_c').val();
	        if( ref == ""){
	            alert("Veuillez entrer une reference");
	            } else {
	            var reg=new RegExp("^[a-zA-Z0-9-_/:,\.]+$", "g");
	            if (!ref.match(reg)){
	                alert("Pour la référence, les seuls caractères autorisés sont : les chiffres, les lettres, et -_/:,.");
	            } else if($("#confirm_ref").val() == 0){
	              alert("La référence saisie existe déjà");

	            } else {
	                $('#formulaire').submit();
	            }
	        }
	 }

	function supprimer(id,ref){
		window.location="produit_modifier.php?id_photo="+id+"&ref="+ref+"action=supprimer_photo";
	}

	function verifref(){
		$.ajax({
			type:'GET',
			url:'ajax/ref.php',
			data:'ref_c='+$('#ref_c').val(),
			success : function(html){
				$("#verification_ref_c").html(html);
			}
		})
	}

	function verifreecriture(url){
		$.ajax({
			type:'GET',
			url:'ajax/reecriture.php',
			data:'url=' + url,
			success : function(html){
				$("#verification_reecriture").html(html);
			}
		})
	}
	function dupliquer(){
	    var ref = prompt("référence du nouveau produit");
	    if(ref != null){
	        $.ajax({
	            type:'GET',
	            url:'ajax/refdupl.php',
	            data:'ref_c='+ref,
	             async: false,
	            success : function(html){
	                if(html == "1"){
						if((ref!="")&&(ref!=null)){
	                    	alert("Référence déjà existante");
	                    	dupliquer();
						}else{
	                    	alert("Veuillez saisir une référence");
	                    	dupliquer();
						}
	                }
	                else{
	                	location="produit_modifier.php?ref=<?php echo $_GET['ref']; ?>&refn="+ref+"&rubrique=<?php echo $_GET['rubrique']; ?>&action=dupliquer";
	                }
	                }
	            })
	    }
	}

	<?php if (intval($produit->id) > 0) { ?>
	function moddecli(obj, id_declidisp) {
		$.ajax({
				type: 'POST',
				url: 'ajax/moddecli.php',
				data: {
					produit: <?php echo $produit->id ?>,
					declidisp: id_declidisp,
					type: obj.checked ? 0 : 1
				},
				async: false
		});
	}
	<?php } ?>

// -->
</script>

<?php
require_once("js/accessoire.php");
require_once("js/contenu_associe.php");

if(isset($existe) && $existe == "1") { ?>
	<script type="text/javascript">
		alert("La reference est deja utilisee");
	</script>
<?php
}
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
  <p><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" alt="-" /><a href="parcourir.php" class="lien04"><?php echo trad('Gestion_catalogue', 'admin'); ?></a>

    <?php
        $parentdesc = new Rubriquedesc();
		$parentdesc->charger($rubrique);

		$parentnom = $parentdesc->titre;

		$res = chemin_rub($rubrique);
		$tot = count($res)-1;

		if($rubrique) {
			echo('<img src="gfx/suivant.gif" width="12" height="9" border="0" alt="-" />');
		}

		while($tot --) {
			echo('<a href="parcourir.php?parent='.$res[$tot+1]->rubrique.'" class="lien04">'.$res[$tot+1]->titre.'</a>');
			echo('<img src="gfx/suivant.gif" width="12" height="9" border="0" alt="-" />');
        }

        $parentdesc = new Rubriquedesc();
		$parentdesc->charger($rubrique);
		$parentnom = $parentdesc->titre;
		?>
			<a href="parcourir.php?parent=<?php echo($parentdesc->rubrique); ?>" class="lien04"><?php echo($parentdesc->titre); ?></a>  <img src="gfx/suivant.gif" width="12" height="9" border="0" alt="-" />

			 <?php if( $ref) { ?>
			<?php echo($produitdesc->titre); ?> / &nbsp;
           <?php echo trad('Modifier', 'admin'); ?><?php } else { ?> <?php echo trad('Ajouter', 'admin'); ?> <?php } ?> </p>

<!-- Début de la colonne de gauche / bloc de la fiche produit -->
<div id="bloc_description">
 <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" id="formulaire" enctype="multipart/form-data">
	<input type="hidden" name="action" value="<?php if(!$ref) { ?>ajouter<?php } else { ?>modifier<?php } ?>" />
	<input type="hidden" name="ref" value="<?php echo($ref); ?>" />
 	<input type="hidden" name="lang" value="<?php echo($lang); ?>" />
 	<input type="hidden" name="rubrique" value="<?php echo($produit->rubrique); ?>" />
	<input type="hidden" name="urlsuiv" id="url" value="0" />

<!-- bloc descriptif du produit -->
		<div class="entete">
			<div class="titre"><?php echo trad('DESCRIPTION_G_PRODUIT', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onClick="envoyer()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>
	<table width="100%" cellpadding="5" cellspacing="0">
    <tr class="claire">
		<th width="133" class="designation" style="height:30px; padding-top:10px;"><?php echo trad('Reference', 'admin'); ?></th>
		<?php
		if($ref){
			?>
			<th style="padding-top:10px;"><?php echo($produit->ref); ?><input type="hidden" id="ref_c" value="<?php echo($produit->ref); ?>" /></th>
			<?php
		}
		else{
			?>
			<th style="padding-top:10px;"> <input type="text" name="ref" id="ref_c" class="form_reference" onBlur="verifref();" /> <span id="verification_ref_c"> </span></th>
			<?php
		}
		?>
	</tr>
	<?php if($ref){ ?>
    <tr class="fonce">
        <td class="designation"><?php echo trad('Changer_langue', 'admin'); ?></td>
        <td>
        <?php
			$langl = new Lang();
			$query = "select * from $langl->table";
			$resul = mysql_query($query);

			while($row = mysql_fetch_object($resul)){
				$langl->charger($row->id);

				$ttrad = new Rubriquedesc();
				if ( (! $ttrad->charger($produit->rubrique, $row->id)) && ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE)
					continue;
	    ?>
	  		 <div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>"><a href="<?php echo($_SERVER['PHP_SELF']); ?>?ref=<?php echo($ref); ?>&amp;rubrique=<?php echo($rubrique); ?>&amp;lang=<?php echo($langl->id); ?>"><img src="gfx/lang<?php echo($langl->id); ?>.gif" alt="-" /></a></div>
	    <?php } ?>

        </td>
   	</tr>
	<?php } ?>
   	<tr class="claire">
        <td class="designation"><?php echo trad('Titre', 'admin'); ?></td>
        <td><input name="titre" id="titre" type="text" class="form_long" value="<?php echo htmlspecialchars($produitdesc->titre); ?>" /></td>
   	</tr>
   	<tr class="fonce">
        <td class="designation"><?php echo trad('Chapo', 'admin'); ?><br /> <span class="note"><?php echo trad('courte_descript_intro', 'admin'); ?></span></td>
        <td><textarea name="chapo" id="chapo" cols="40" rows="2" class="form_long"><?php echo($produitdesc->chapo); ?></textarea></td>
   	</tr>
   	<tr class="claire">
        <td class="designation"><?php echo trad('Description', 'admin'); ?><br /> <span class="note"><?php echo trad('description_complete', 'admin'); ?></span></td>
        <td><textarea name="description" id="description" rows="5" cols="20" style="width:100%;"><?php echo($produitdesc->description); ?></textarea></td>
   	</tr>
   	<tr class="fonce">
        <td class="designation"><?php echo trad('PS', 'admin'); ?><br /> <span class="note"><?php echo trad('champs_info_complementaire', 'admin'); ?></span></td>
        <td><textarea name="postscriptum" id="postscriptum" cols="40" rows="2" class="form_long"><?php echo($produitdesc->postscriptum); ?></textarea>
</td>
   	</tr>
   		<tr class="claire">
        <td class="designation"><?php echo trad('Appartenance', 'admin'); ?><br /> <span class="note"><?php echo trad('deplacer2', 'admin'); ?></span></td>
        <td style="vertical-align:top;"><select name="rubrique" id="rubrique" class="form_long">
        	<option value="0"><?php echo trad('A la racine', 'admin'); ?></option>
          <?php
          if($ref) echo arbreOption(0, 1, $produit->rubrique, 0);
          else echo arbreOption(0, 1, $rubrique, 0);
          ?>
        </select></td>
   	</tr>
    </table>

<!-- bloc des caractéristiques de base du produit -->
    <div class="entete">
			<div class="titre"><?php echo trad('CARACTERISTIQUES_PRODUITS', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onClick="envoyer()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
	</div>
	<table width="100%" cellpadding="5" cellspacing="0">

   	<tr class="claire">
        <th width="133" class="designation"><?php echo trad('Prix_TTC', 'admin'); ?></th>
        <th width="133"><input name="prix" id="prix" type="text" class="form_court" value="<?php echo($produit->prix); ?>" /></th>
        <th class="designation" width="133" ><?php echo trad('TVA', 'admin'); ?></th>
        <th width="133"><input name="tva" id="tva" type="text" class="form_court" value="<?php echo($tva); ?>" /></th>
   	</tr>
   	<tr class="fonce">
        <td class="designation"><?php echo trad('Prix_promo_pourcent', 'admin'); ?></td>
        <td><input type="text" name="remise" id="remise" value="" class="form_court"/></td>
        <?php
		echo('<td class="designation">'.trad('Nouveaute','admin').'</td>');
        echo('<td><input name="nouveaute" id="nouveaute" type="checkbox" class="form"');
		if($produit->nouveaute) echo('checked');
		echo('/></td>');
		?>
   	</tr>
   		<tr class="claire">
        <td class="designation"><?php echo trad('Prix_promo_TTC', 'admin'); ?></td>
        <td><input name="prix2" id="prix2" type="text" class="form_court" value="<?php echo($produit->prix2); ?>" /></td>
		<?php echo('<td class="designation">'.trad('En_promotion','admin').'</td>');
		echo('<td><input name="promo" id="promo" type="checkbox" class="form"');
		if($produit->promo) echo('checked');
		echo('/></td>');
		?>
   	</tr>
   		<tr class="fonce">
        <td class="designation"><?php echo trad('Poids', 'admin'); ?></td>
        <td><input type="text" name="poids" id="poids" class="form_court" value="<?php echo($produit->poids); ?>" /></td>
        <?php echo('<td class="designation">'.trad('En_ligne','admin').'</td>');
		echo('<td><input name="ligne" id="ligne" type="checkbox" class="form"');
		if($produit->ligne || $produit->ligne == '') echo('checked');
		echo('/></td>');
		?>
   	</tr>
   	<tr class="claire">
        <td class="designation"><?php echo trad('Ecotaxe', 'admin'); ?></td>
        <td><input name="ecotaxe" id="ecotaxe" type="text" class="form_court" value="<?php echo($produit->ecotaxe); ?>" /></td>
        <?php echo('<td class="designation">'.trad('Stock','admin').'</td>');
		echo('<td><input name="stock" id="stock" type="text" class="form_court" value="');
		if($produit->stock != '') echo($produit->stock);
		else echo(1);
		echo('"/></td>');
		?>
   	</tr>
</table>

<?php
	if($ref) {
?>
<!-- début du bloc d'informations sur le produit -->
	<?php
		$produit = new Produit();
		$produit->charger($ref);

		$datemodif = strftime("%d/%m/%Y %H:%M:%S", strtotime($produit->datemodif));
	?>
		<div class="entete">
			<div class="titre" style="cursor:pointer" onClick="$('#pliantinfos').show('slow');"><?php echo trad('INFO_PRODUIT', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onClick="envoyer()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>
		<div class="blocs_pliants_prod" id="pliantinfos">

			<ul class="lignesimple">
				<li class="cellule_designation" style="width:128px; padding:5px 0 0 5px; background-image:url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
					ID
				</li>
				<li class="cellule" style="width:450px; padding: 5px 0 0 5px; background-image:url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
					<?php echo($produit->id); ?>
				</li>
			</ul>

			<ul class="lignesimple">
				<li class="cellule_designation" style="width:128px; padding:5px 0 0 5px;"><?php echo trad('URL_reecrite', 'admin'); ?></li>
				<li class="cellule" style="width:450px;padding: 5px 0 0 5px;"><input type="text" name="urlreecrite" onKeyUp="if(event.keyCode==13) verifreecriture(this.value);" value="<?php echo  htmlspecialchars(rewrite_prod("$produit->ref", $lang)); ?>" class="form_reecriture" /><span id="verification_reecriture">&nbsp;</span></li>
			</ul>
			<ul class="lignesimple">
				<li class="cellule_designation" style="width:128px; padding: 5px 0 0 5px;"><?php echo trad('Derniere_modif', 'admin'); ?></li>
				<li class="cellule" style="width:450px;padding: 5px 0 0 5px;"><?php echo "le $datemodif"; ?></li>
			</ul>
<div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantinfos').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>
<!-- fin du bloc de gestion des informations du produit -->

 		<!--DEBUT du bloc de gestion des CARACTERISTIQUES AJOUTEES-->
		<div class="entete">
			<div class="titre" style="cursor:pointer" onClick="$('#pliantcaracteristiques').show('slow');"><?php echo trad('CARACTERISTIQUES_AJOUTEES', 'admin'); ?></div>
		</div>

<div class="blocs_pliants_prod" id="pliantcaracteristiques">
	<?php
   	$rubcaracteristique = new Rubcaracteristique();
   	$caracteristiquedesc = new Caracteristiquedesc();
   	$caracdisp = new Caracdisp();
    $caracdispdesc = new Caracdispdesc();
 	$caracteristique = new Caracteristique();

   	$query = "select * from $rubcaracteristique->table,$caracteristique->table  where $rubcaracteristique->table.caracteristique=$caracteristique->table.id and $rubcaracteristique->table.rubrique='" . $rubrique . "' order by $caracteristique->table.classement";
   	$resul = mysql_query($query);

   	$caracval = new Caracval();

   	while($row = mysql_fetch_object($resul)){
		$caracval = new Caracval();
   		$caracteristiquedesc->charger($row->caracteristique);
   		$caracval->charger($produit->id, $row->caracteristique);

   		$query2 = "select c.* from $caracdisp->table c left join $caracdispdesc->table cd on cd.caracdisp = c.id and cd.lang = $lang where c.caracteristique='$row->caracteristique' order by cd.classement";
   		$resul2 = mysql_query($query2);
   		$nbres = mysql_num_rows($resul2);
  		if(!$nbres) { ?>

			<ul class="ligne1">
				<li class="cellule_designation" style="width:290px;"><?php echo($caracteristiquedesc->titre); ?></li>
				<li class="cellule">
        			<input type="hidden" name="typecaract<?php echo($row->caracteristique); ?>" id="typecaract<?php echo($row->caracteristique); ?>" value="v" />
        			<input type="text" class="form_caracterisques_ajoutees" name="caract<?php echo($row->caracteristique); ?>" id="caract<?php echo($row->caracteristique); ?>" value="<?php echo(htmlspecialchars($caracval->valeur)); ?>" />
				</li>
        	</ul>

        <?php
		} else { ?>
			<ul class="lignemultiple">
				<li class="cellule_designation_multiple" style="width:290px; padding:5px 0 0 5px;"><?php echo($caracteristiquedesc->titre); ?></li>
				<li class="cellule"  style="padding:5px 0 0 5px;">
        			<input type="hidden" name="typecaract<?php echo($row->caracteristique); ?>" id="typecaract<?php echo($row->caracteristique); ?>" value="c" />
        			<select name="caract<?php echo($row->caracteristique); ?>[]" id="caract<?php echo($row->caracteristique); ?>" class="form_caracterisques_ajoutees"
						size="5" multiple="multiple">
          			<?php while($row2 = mysql_fetch_object($resul2)) {
     						$caracdispdesc->charger_caracdisp($row2->id);
          					$caracval->charger_caracdisp($produit->id, $row2->caracteristique, $caracdispdesc->caracdisp);

							if($caracdispdesc->caracdisp == $caracval->caracdisp) $selected='selected="selected"';
							else $selected='';

							if($caracteristiquedesc->titre == 'Autre rubrique') {
								//$caracdispdesc->caracdisp = $caracdispdesc->titre;
								$Rubriquedesc = new Rubriquedesc();
								$Rubriquedesc->charger($caracdispdesc->titre);
								$rubtitre = ' '.$Rubriquedesc->titre;
							}

							echo('<option value="'.$caracdispdesc->caracdisp.'" '.$selected.'>'.$caracdispdesc->titre.$rubtitre.'</option>');
							$rubtitre='';
						} ?>
        			</select>
        		</li>
        	</ul>
	<?php }
	}
?>
<div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantcaracteristiques').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>
<!--FIN du bloc de gestion des CARACTERISTIQUES AJOUTEES -->

 <!-- début du bloc de gestion des déclinaisons simple -->
		<div class="entete">
			<div class="titre" style="cursor:pointer" onClick="$('#pliantdeclinaisons').show('slow');"><?php echo trad('GESTION_DECLINAISONS', 'admin'); ?></div>
		</div>
<div class="blocs_pliants_prod" id="pliantdeclinaisons">
     <?php
   	$rubdeclinaison = new Rubdeclinaison();
   	$declinaisondesc = new Declinaisondesc();
   	$declidisp = new Declidisp();
    $declidispdesc = new Declidispdesc();
 	$declinaison = new Declinaison();

   	$query = "select * from $rubdeclinaison->table,$declinaison->table  where $rubdeclinaison->table.declinaison=$declinaison->table.id and $rubdeclinaison->table.rubrique='" . $rubrique . "' order by $declinaison->table.classement";   	$resul = mysql_query($query);


   	while($row = mysql_fetch_object($resul)){

   		$declinaisondesc->charger($row->declinaison);
   		$query2 = "select * from $declidisp->table,$declidispdesc->table where $declidisp->table.declinaison='$row->declinaison' and $declidispdesc->table.declidisp=$declidisp->table.id order by $declidispdesc->table.classement";
   		$resul2 = mysql_query($query2);
   		$nbres = mysql_num_rows($resul2);
   ?>
			<ul class="ligne1">
				<li class="cellule" style="width:300px;"><?php echo($declinaisondesc->titre); ?></li>
				<li class="cellule" style="width:80px;"><?php echo trad('Stock', 'admin'); ?></li>
				<li class="cellule" style="width:80px;"><?php echo trad('Surplus', 'admin'); ?></li>
				<li class="cellule" style="width:80px;"><?php echo trad('Active', 'admin'); ?></li>
				<input type="hidden" name="typedeclit<?php echo($row->declinaison); ?>" value="c" />
			</ul>


          <?php while($row2 = mysql_fetch_object($resul2)){
     		 	$declidispdesc->charger_declidisp($row2->id);

     		 	$stock = new Stock();
     		 	$stock->charger($row2->id, $produit->id);
     	?>
		<?php
			$exdecprod = new Exdecprod();
			$res = $exdecprod->charger($produit->id, $row2->id);
		?>

			<ul class="lignesimple">
				<li class="cellule" style="width:300px; padding: 5px 0 0 5px;"><?php echo($declidispdesc->titre); ?></li>
				<li class="cellule_prix" style="padding: 5px 0 0 5px;"><input type="text" name="stock<?php echo($row2->id); ?>" value="<?php echo($stock->valeur); ?>" size="4" class="form" /></li>
				<li class="cellule_prix" style="padding: 5px 0 0 5px;"><input type="text" name="surplus<?php echo($row2->id); ?>" value="<?php echo($stock->surplus); ?>" size="4" class="form" /></li>
				<li class="cellule_prix"  style="padding: 5px 0 0 5px;"><input type="checkbox" <?php echo $res ? '' : 'checked="checked"' ?> name="moddecli-<?php echo($declidispdesc->declidisp); ?>" onClick="moddecli(this, <?php echo($declidispdesc->declidisp); ?>);" /></li>
			</ul>
	<?php }  } ?>
<div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantdeclinaisons').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>

<!-- fin du bloc de gestion des declinaisons -->

<!-- début du bloc de gestion des accessoires -->
<div class="entete">
	<div class="titre" style="cursor:pointer" onClick="$('#pliantaccessoires').show('slow');"><?php echo trad('GESTION_ACCESSOIRES', 'admin'); ?></div>
</div>

<div class="blocs_pliants_prod" id="pliantaccessoires">
	<ul class="ligne1">
		<li class="cellule">
		<select class="form_select" id="accessoire_rubrique" onChange="charger_listacc(this.value);">
     	<option value="">&nbsp;</option>
     	<?php echo arbreOption(0, 1, 0, 0); ?>
		</select></li>

		<li class="cellule">
			<select class="form_select" id="select_prodacc">
				<option value="">&nbsp;</option>
			</select>
		</li>
		<li class="cellule"><a href="javascript:accessoire_ajouter($('#select_prodacc').val())"><?php echo trad('AJOUTER', 'admin'); ?></a></li>
	</ul>

	<ul id="accessoire_liste">
		<?php
		lister_accessoires($_GET['ref']);
        ?>
	 </ul>
<div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantaccessoires').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>
<!-- fin du bloc de gestion des accessoires -->

<!-- début du bloc de gestion des contenus associés -->
<div class="entete">
	<div class="titre" style="cursor:pointer" onClick="$('#pliantcontenusassocies').show('slow');"><?php echo trad('GESTION_CONTENUS_ASSOCIES', 'admin'); ?></div>
</div>
<div class="blocs_pliants_prod" id="pliantcontenusassocies">
		<ul class="ligne1">
			<li class="cellule">
			<select class="form_select" id="contenuassoc_dossier" onChange="charger_listcont(this.value, 1,'<?php echo $produit->ref; ?>');">
	     	<option value="">&nbsp;</option>
	     	 <?php echo arbreOption_dos(0, 1, 0, 0, 1); ?>
			</select></li>

			<li class="cellule">
			<select class="form_select" id="select_prodcont">
			<option value="">&nbsp;</option>
			</select>
			</li>

			<li class="cellule"><a href="javascript:contenu_ajouter($('#select_prodcont').val(), 1,'<?php echo $produit->ref; ?>')"><?php echo trad('AJOUTER', 'admin'); ?></a></li>
		</ul>

		<ul id="contenuassoc_liste">
		<?php lister_contenuassoc(1, $produit->ref); ?>
		</ul>

<div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantcontenusassocies').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>
<!-- fin du bloc de gestion des contenus associés -->

<!-- début du bloc point d'entrée -->
<div class="patchplugin">
<?php ActionsAdminModules::instance()->inclure_module_admin("produitmodifier"); ?>
</div>
<!-- fin du bloc point d'entrée -->

<?php }  ?>
</form>

</div>
<?php if($ref != ""){?>

<!-- bloc de gestion des photos et documents / colonne de droite -->
<div id="bloc_photos">
<!-- début du bloc Boite à outils du produit -->
<div class="entete">
	<div class="titre"><?php echo trad('BOITE_OUTILS', 'admin'); ?></div>
</div>
<div class="bloc_transfert">
	<div class="claire">
		<div class="champs outils" style="padding-top:10px; width:375px;">
			<?php
			$query = "select max(classement) as maxClassement from $produit->table where rubrique='" . $rubrique . "'";
			$resul = mysql_query($query, $produit->link);
			$classementmax =  mysql_result($resul, 0, "maxClassement");

			$query = "select min(classement) as minClassement from $produit->table where rubrique='" . $rubrique . "'";
			$resul = mysql_query($query, $produit->link);
			$classementmin =  mysql_result($resul, 0, "minClassement");

			$classement=$produit->classement;
			if($classement>$classementmin) {
				$precedent=$classement;
				do {
					$precedent--;
					$query = "select * from $produit->table where rubrique='" . $rubrique . "' and classement='" . $precedent . "' ";
					$resul = mysql_query($query, $produit->link);
				} while(!mysql_num_rows($resul) && $precedent>$classementmin);

				if(mysql_num_rows($resul) !=0) {
					 $refprec =  mysql_result($resul,0,'ref');
			?>
				<a href="produit_modifier.php?ref=<?php echo $refprec;?>&amp;rubrique=<?php echo $rubrique;?>" >
					<img src="gfx/precedent.png" alt="Produit précédent" title="Produit précédent" style="padding:0 5px 0 0;margin-top:-5px;height:38px;"/>
				</a>
			<?php
				}
			} ?>
			<a title="Voir le produit en ligne" href="<?php echo urlfond("produit", "ref=$ref&amp;id_rubrique=$rubrique", true); ?>" target="_blank" >
				<img src="gfx/site.png" alt="Voir le produit en ligne" title="Voir le produit en ligne"/>
			</a>
			<a href="#" onClick="dupliquer();">
				<img src="gfx/dupliquer.png" alt="Dupliquer la fiche produit" title="Dupliquer la fiche produit"/>
			</a>
			<a href="#" onClick="envoyer();">
				<img src="gfx/valider.png" alt="Enregistrer les modifications" title="Enregistrer les modifications"/>
			</a>
			<a href="#" onClick="$('#url').val('1'); envoyer(); ">
				<img src="gfx/validerfermer.png" alt="Enregistrer les modifications et fermer la fiche" title="Enregistrer les modifications et fermer la fiche"/>
			</a>

			<?php
				if($classement!=$classementmax) {

					$precedent=$classement;
					do{
					$precedent++;
					$query = "select * from $produit->table where rubrique='" . $rubrique . "' and classement='" . $precedent . "' ";
					$resul = mysql_query($query, $produit->link);
					}
					while(!mysql_num_rows($resul) && $precedent<$classementmax);
					if(mysql_num_rows($resul) !=0){
						 $refprec =  mysql_result($resul,0,"ref");
			?>
			<a href="produit_modifier.php?ref=<?php echo $refprec;?>&amp;rubrique=<?php echo $rubrique;?>" ><img src="gfx/suivant.png" alt="Produit suivant" title="Produit suivant" style="padding:0 5px 0 0;"/></a>
			<?php
					}
				} ?>
   		</div>
   	</div>
</div>
<!-- fin du bloc Boite à outils du produit-->

<!-- début du bloc de transfert des images du produit-->
<div class="entete" style="margin-top:10px;">
	<div class="titre"><?php echo trad('GESTION_PHOTOS', 'admin'); ?></div>
</div>

<?php $images_adm->bloc_transfert() ?>

<!-- fin du bloc de transfert des images du produit-->

<!-- début du bloc de gestion des photos du produit -->
<div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantsphotos').show('slow');"><img src="gfx/fleche_accordeon_img_dn.gif" /></div>
<div class="blocs_pliants_photo" id="pliantsphotos">

	<?php $images_adm->bloc_gestion() ?>

	<div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantsphotos').hide();"><img src="gfx/fleche_accordeon_img_up.gif" /></div>
</div>
<!-- fin du bloc de gestion des photos du produit -->


<!-- début du bloc de transfert des documents du produit -->
	<div class="entete" style="margin-top:10px;">
			<div class="titre"><?php echo trad('GESTION_DOCUMENTS', 'admin'); ?></div>
	</div>

	<?php $documents_adm->bloc_transfert() ?>

<!-- fin du bloc transfert des documents du produit -->
<!-- début du bloc de gestion des documents du produit -->
<div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantsfichier').show('slow');"><img src="gfx/fleche_accordeon_img_dn.gif" /></div>
<div class="blocs_pliants_fichier" id="pliantsfichier">

	<?php $documents_adm->bloc_gestion() ?>

    <div class="bloc_fleche" style="cursor:pointer" onClick="$('#pliantsfichier').hide();"><img src="gfx/fleche_accordeon_img_up.gif" /></div>
</div>
</div>
<?php
}
?>

</div>
<?php require_once("pied.php"); ?>
</div>
<!-- </div> -->
<!-- -->
<script type="text/javascript" src="../lib/jquery/jquery.accordion.js"></script>
<script type="text/javascript">
jQuery().ready(function(){
	// applying the settings
	jQuery('#blocs_pliants_prod').accordion({
		active: 'h3.selected',
		header: 'h3.head',
		alwaysOpen: false,
		animated: true,
		showSpeed: 400,
		hideSpeed: 400
	});

});
</script>
<script type="text/javascript">
$(document).ready(function(){

	$('#remise').live('keyup',function(){
		var pourcent = 1-($(this).val()/100);
		var prix2 = $('#prix').val()*pourcent;
		console.log($(this).val());
		prix2 = (Math.round(prix2*100))/100;
		//console.log(prix2);
		$('#prix2').val(prix2);
	});

});
</script>
<!-- -->
</body>
</html>
