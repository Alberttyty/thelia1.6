<?php

include_once realpath(dirname(__FILE__)) . '/../../../fonctions/authplugins.php';

autorisation('paybox');

include_once realpath(dirname(__FILE__)) . '/Paybox.class.php';


$paybox = new Paybox();

switch($action){
    case 'modifier':
        $paybox->modifyAll();
        break;
}


$listes = $paybox->charger_admin(array('PBX_HASH','PBX_RETOUR'));
?>
<!-- Modele vierge de mise en page pour plugin backoffice de thelia -->

<div id="contenu_int"> 
<!-- fil d'ariane de l'admin -->
	<p align="left"><a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=paybox" class="lien04">Paybox</a>              
	</p>
<!--  colonne de gauche -->
<div id="bloc_description">
<!-- titre -->
<form method="post" id="formulaire_paybox" action="module.php?nom=paybox">
    <input type="hidden" name="action" value="modifier">
	<div class="entete_liste_config">
		<div class="titre">Paybox</div>
                <div class="fonction_valider">
                    <a onclick="document.getElementById('formulaire_paybox').submit()" href="#">VALIDER LES MODIFICATIONS</a>
                </div>
	</div>
<!-- entete -->
	<ul class="Nav_bloc_description">
		<li style="height:25px; width:300px;">Description</li>
		<li style="height:25px; border-left:1px solid #96A8B5;">Valeur</li>
	</ul>
<!-- bloc avec bordure inferieure -->

	<div class="bordure_bottom">
            <?php 
            $i = 0;
            foreach($listes as $liste){
                $i++;
                if($i%2)
                    $style = "ligne_claire_BlocDescription";
                else
                    $style = "ligne_fonce_BlocDescription";
                ?>
                <ul class="<?php echo $style ?>">
		    <li style="width:301px"><?php
                        if(!empty($liste->description)) echo $liste->description;
                        else echo $liste->key;
                    ?></li>
		    <li style="border-left:1px solid #C4CACE; width:90px">
                        <input type="text" name="value_<?php echo $liste->key; ?>" value="<?php echo $liste->value; ?>">
                    </li>
		</ul>
                
            <?php    
            }
            ?>
	</div>

</div>
</form>
<!-- fin colonne de gauche -->

<!-- fin du bloc colonne de droite -->
</div>