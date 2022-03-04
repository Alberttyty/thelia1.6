<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("newslettermailjetv3");

	include_once(realpath(dirname(__FILE__)) . "/Newslettermailjetv3.class.php");
?>

<?php

  $newslettermailjet = new Newslettermailjetv3();

  $response=$newslettermailjet->mj->liststatistics();  
  
  $lists=$response->Data;
	
?>
<div id="contenu_int">

	    <p><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Newsletter</a> </p>

<div id="bloc_informations">
	<ul>
		<li class="entete_configuration">LISTES CHEZ MAILJET</li>

<?php
  $couleur='fonce';
  foreach($lists as $k => $v){
  
  if($couleur=='fonce') $couleur='claire';
  else $couleur='fonce';  
?>
    
    <li class="<?php echo $couleur; ?>" style="width:204px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo $v->Name; ?></li>
		<li class="<?php echo $couleur; ?>" style="width:90px;">ID <?php echo $v->ID; ?></li>
    
<?php
  }
?>
		
			</ul>
</div>

</div>