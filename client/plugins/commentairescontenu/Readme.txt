Ce plugin permet de grer des commentaires sur les produits.

Veuillez simplement glisser le rpertoire commentaires dans le dossier client/plugins/ de votre Thelia.
Vous pouvez aussi utiliser l'assistant d'import de plugins dans Configuration/Gestion des plugins.
Activez-le ensuite dans le menu Configuration/Gestion des plugins dans votre interface d'administration

Exemple de formulaire d'ajout :

<form action="#" method="post">
	<input type="hidden" name="action" value="ajcommentairecontenu" />
	<input type="hidden" name="commentaire_id" value="#ID" />
	<input type="hidden" name="id" value="#ID" />
	Nom : <input type="text" name="commentaire_nom" /><br />
	Message : <input type="text" name="commentaire_message" /><br />
	<input type="submit" value="OK" />
</form>


Param entre
id : rfrence du produit

Param sortie
#NOM : nom de la personne qui a post un commentaire
#MESSAGE : contenu du commentaire
#DATE : date du commentaire
#HEURE : heure du commentaire


Exemple d'une boucle d'affichage

<THELIA_comment type="COMMENTAIRESCONTENU" id="#CONTENU_ID">                                                      
Message de #NOM : #MESSAGE <br />
#HEURE #DATE 
</THELIA_comment>

Auteur : Etienne
