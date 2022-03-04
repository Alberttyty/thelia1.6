Module de newsletter complet en liaison avec Mailjet.

Veuillez simplement glisser le répertoire mailjet dans le dossier client/plugins de votre Thelia puis activer le plugin.

Editez ensuite le fichier config.php pour renseigner la cle et le mot de passe secret de votre compte mailjet.

Auteur : Yoan De Macedo
	 yoan@octolys.fr


<form action="#VARIABLE(urlsite)/?fond=newsletter" method="post" />
	<input type="hidden" name="action" value="newsletter_ajout" />
	Votre e-mail : <input type="text" name="newsletter_email" />
	<input type="submit" value="OK" />
</form>


<input type="checkbox" name="newsletter_email"  value="true" />