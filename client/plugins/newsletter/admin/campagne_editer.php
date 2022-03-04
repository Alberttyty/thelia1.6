<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../../fonctions/authplugins.php");

	autorisation("newsletter");

	include_once(realpath(dirname(__FILE__)) . "/../Newsletter.class.php");
  
	$campagne = new Newsletter_campagne();

  if(!$id) {
  
    $campagne->titre="";
    
    if(file_exists("../template/newsletter.html"))
		{
    $campagne->texte=file_get_contents("../template/newsletter.html");
    }
    else
    {
    $campagne->texte=file_get_contents("../client/plugins/newsletter/template/newsletter.html");
    }

  
  }
  else {
    
    $campagne->charger_id($id);
  
  }
  
?>
<p><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=newsletter" class="lien04">Newsletter</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=newsletter&amp;action_newsletter=campagne" class="lien04">Gestion des campagnes</a></p>


<div id="bloc_description" style="width:976px;">
 <form action="module.php?nom=newsletter&amp;action_newsletter=campagne" method="post" id="formulaire" enctype="multipart/form-data">
	<input type="hidden" name="action_newsletter" value="<?php if(!$id) { ?>creer_campagne<?php } else { ?>modifier_campagne<?php } ?>" />
	<input type="hidden" name="nom" value="newsletter" />
  <input type="hidden" name="id" value="<?php echo($id); ?>" />
		<div class="entete">
			<div class="titre">DESCRIPTION DE LA CAMPAGNE</div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>
	<table width="100%" cellpadding="5" cellspacing="0">
   	<tr class="fonce">
        <td class="designation"><?php echo trad('Titre', 'admin'); ?></td>
        <td><input name="titre" id="titre" type="text" class="form_long" value="<?php echo htmlspecialchars($campagne->titre); ?>" /></td>
   	</tr>
   	<tr class="fonce">
        <td class="designation">Texte</td>
        <td>
        <textarea name="description" id="description" cols="40" rows="40"><?php echo($campagne->texte); ?></textarea></td>
   	</tr>
    </table>
  </form>   
</div>