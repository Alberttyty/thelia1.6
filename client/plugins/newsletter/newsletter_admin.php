<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("newsletter");

	include_once(realpath(dirname(__FILE__)) . "/Newsletter.class.php");
	include_once(realpath(dirname(__FILE__)) . "/admin/action.php");
?>

<?php

	//$newsletter = new Newsletter();
	//echo  $newsletter->stat("test", "sent");
	
	//$newsletter->mail("", "", "", "", "");
	
?>
<div id="contenu_int">
<?php
	if($_REQUEST['action_newsletter'] == ""){
?>
	    <p><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Newsletter</a> </p>

<div id="bloc_informations">
	<ul>
		<li class="entete_configuration">FONCTIONS NEWSLETTER</li>
    
    <li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;">Gestion des campagnes</li>
		<li class="claire" style="width:72px;"><a href="module.php?nom=newsletter&action_newsletter=campagne"><?php echo trad('editer'); ?> </a></li>
		
 		<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;">Gestion des listes</li>
		<li class="fonce" style="width:72px;"><a href="module.php?nom=newsletter&action_newsletter=liste"><?php echo trad('editer'); ?> </a></li>
		
		<li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;">Gestion des d&eacute;sinscriptions</li>
		<li class="claire" style="width:72px;"><a href="module.php?nom=newsletter&action_newsletter=desinscription"><?php echo trad('editer'); ?> </a></li>
		
		<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;">Gestion des statistiques</li>
		<li class="fonce" style="width:72px;"><a href="module.php?nom=newsletter&action_newsletter=statistique"><?php echo trad('editer'); ?> </a></li>
	</ul>
</div>
<?php
	} else {
	
		switch($_REQUEST['action_newsletter']){
			case 'liste' : include("../client/plugins/newsletter/admin/liste.php"); break;
			case 'creer_liste' : include("../client/plugins/newsletter/admin/liste.php"); break;
			case 'supprimer_liste' : include("../client/plugins/newsletter/admin/liste.php"); break;
			case 'ajouter_email' : include("../client/plugins/newsletter/admin/liste.php"); break;
			case 'importer_base' : include("../client/plugins/newsletter/admin/liste.php"); break;
      case 'importer_clients' : include("../client/plugins/newsletter/admin/liste.php"); break;
			case 'importer_csv' : include("../client/plugins/newsletter/admin/liste.php"); break;
			case 'supprimer_email' : include("../client/plugins/newsletter/admin/liste.php"); break;
			case 'editer_liste' : include("../client/plugins/newsletter/admin/liste.php"); break;
			case 'desinscription_annulation' : include("../client/plugins/newsletter/admin/desinscription.php"); break;
			case 'desinscription' : include("../client/plugins/newsletter/admin/desinscription.php"); break;
			case 'campagne' : include("../client/plugins/newsletter/admin/campagne.php"); break;
			case 'dupliquer' : include("../client/plugins/newsletter/admin/campagne.php"); break;
			case 'envoyer' : include("../client/plugins/newsletter/admin/campagne.php"); break;
      case 'creer_campagne' : include("../client/plugins/newsletter/admin/campagne.php"); break;
      case 'supprimer_campagne' : include("../client/plugins/newsletter/admin/campagne.php"); break;
      case 'modifier_campagne' : include("../client/plugins/newsletter/admin/campagne.php"); break;
      case 'tester' : include("../client/plugins/newsletter/admin/campagne.php"); break;
      case 'campagne_editer' : include("../client/plugins/newsletter/admin/campagne_editer.php"); break;
			case 'statistique' : include("../client/plugins/newsletter/admin/statistique.php"); break;
		}
	
	}
?>	
</div>