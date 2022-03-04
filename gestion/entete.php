<div id="entete">
		<div class="logo">
				<a href="accueil.php"><img src="gfx/thelia_logo.jpg" alt="THELIA solution e-commerce" /></a>
		</div>
		<dl class="Blocmoncompte">
				<dt><a href="index.php?action=deconnexion" ><?php echo trad('Deconnexion', 'admin'); ?></a></dt>
				<dt> | </dt>
				<dt><strong><?php echo($_SESSION["util"]->prenom); ?> <?php echo($_SESSION["util"]->nom); ?></strong> </dt>
		</dl>
		<div class="Blocversion">V <?php echo rtrim(preg_replace("/(.)/", "$1.", Variable::lire('version')), "."); ?></div>
</div>

<nav id="menuGeneral" class="clearfix">
		<ul><li id="menu">
						<a href="accueil.php" <?php if($menu == "accueil") { ?>class="selected"<?php } ?>><?php echo trad('Accueil', 'admin'); ?></a>

				</li><?php	if(est_autorise("acces_clients")) { ?><li id="menu1">
		        <a href="client.php" <?php if($menu == "client") { ?>class="selected"<?php } ?>><?php echo trad('Clients', 'admin'); ?></a>

				</li><?php } if(est_autorise("acces_commandes")) { ?><li id="menu2">
						<a href="commande.php" <?php if($menu == "commande") { ?>class="selected"<?php } ?>><?php echo trad('Commandes', 'admin'); ?></a>

				</li><?php } if(est_autorise("acces_catalogue")) { ?><li id="menu3">
						<a href="parcourir.php" <?php if($menu == "catalogue") { ?>class="selected"<?php } ?>><?php echo trad('Catalogue', 'admin'); ?></a>

				</li><?php } if(est_autorise("acces_contenu")) { ?><li id="menu4">
						<a href="listdos.php" <?php if($menu == "contenu") { ?>class="selected"<?php } ?>><?php echo trad('Contenu', 'admin'); ?></a>

				</li><?php } if(est_autorise("acces_codespromos")) { ?><li id="menu5">
						<a href="promo.php" <?php if($menu == "paiement") { ?>class="selected"<?php } ?>><?php echo trad('Codes_promos', 'admin'); ?></a>

				</li><?php } if(est_autorise("acces_configuration")) { ?><li id="menu6">
						<a href="configuration.php" <?php if($menu == "configuration") { ?>class="selected"<?php } ?>><?php echo trad('Configuration', 'admin'); ?></a>

				</li><?php } if(est_autorise("acces_modules")) { ?><li id="menu7">
						<a href="module_liste.php" <?php if($menu == "plugins") { ?>class="selected"<?php } ?>><?php echo trad('Modules', 'admin'); ?></a>
				</li><?php } ?>
		</ul>

		<?php if(est_autorise("acces_rechercher")) { ?>
    <div id="moteur_recherche">
    		<form action="recherche.php" method="post">
            <div class="bouton_recherche">
	         			<input type="image" src="gfx/icone_recherche.jpg" alt="Valider la recherche" />
	         	</div>
            <div class="champs_recherche">
	         			<input type="text" name="motcle" value="<?php echo trad('Rechercher', 'admin'); ?>" class="zonerecherche" onClick="this.value=''" size="24" />
	         	</div>
				</form>
    </div>
    <?php } ?>
</nav>
