Ce plugin permet d'afficher la liste des ventes associées à un produit.
Exemple :
Les personnes qui ont acheté "Produit A" ont aussi acheté :
	- "Produit B"
	- "Produit C"
	- ...

Veuillez simplement glisser le répertoire ventassoc dans le dossier client/plugins/ de votre Thelia.
Vous pouvez aussi utiliser l'assistant d'import de plugins dans Configuration/Gestion des plugins.
Activez-le ensuite dans le menu Configuration/Gestion des plugins dans votre interface d'administration.

<THELIA_vassoc type="VENTASSOC" ref="#PRODUIT_REF" classement="prixmax">
	
	Les personnes ayant achetees #PRODUIT_NOM ont aussi acheté :
	
	<THELIA_detprod type="PRODUIT" ref="#REF">
		#TITRE
		<a href="#PANIER">Ajouter</a>
	</THELIA_detprod> 	

</THELIA_vassoc>

Param entrée
ref		: la référence du produit dont on souhaite connaitre les ventes qui 
		  lui sont associées (produit source)
num		: nombre de produits à afficher dans la boucle
classement	: permet de choisir l'ordre d'affichage des produits
		  	prixmin   --> affiche les produits du moins cher au plus cher
		  	prixmax   --> affiche les produits du plus cher au moins cher
		  	aleatoire --> affiche les produits de façon aléatoire (!)
nocache		: (facultatif) si nocache=1, alors la boucle en question ne sera pas
		  mise en cache. (Utile si vous choisissez un affichage aléatoire)

Param sortie
ref		: la référence de chaque produit associé au produit source



Remarque	:
Avec l'ajout du systeme de cache dans Thelia, si vous créez une boucle avec classement="aleatoire",
elle sera mise en cache lors de son premier chargement, et l'affichage "aleatoire" restera le même 
à chaque chargement de la page (pour la durée de la session du visiteur).
Pour éviter cela, vous pouvez demander à Thelia de "forcer" la regénération de la boucle grâce au 
paramètre nocache="1".

<THELIA_vassoc type="VENTASSOC" ref="#PRODUIT_REF" classement="aleatoire" nocache="1">
	
	Les personnes ayant achetees #PRODUIT_NOM ont aussi acheté :
	
	<THELIA_detprod type="PRODUIT" ref="#REF">
		#TITRE
		<a href="#PANIER">Ajouter</a>
	</THELIA_detprod> 	

</THELIA_vassoc>

Auteur : Matthieu Mallet