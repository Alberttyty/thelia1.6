Ce plugin de type filtre, permet de tester si la valeur est supérieure, inférieur, supérieur ou égale et inférieur ou égale à une variable fixe.

Exemple :

#FILTRE_sup(#PANIER_NBART||10||La commande fait plus de 10 articles)

Rsultat :
"La commande fait plus de 10 articles" si le panier contient plus de 10 articles, sinon on naffiche rien


#FILTRE_supegal(#PANIER_NBART||10||La commande fait plus de 10 articles)

Rsultat :
"La commande fait plus de 10 articles" si le panier contient plus de 10 articles ou exactement 10 articles, sinon on naffiche rien


#FILTRE_inf(#PANIER_NBART||10||La commande fait moins de 10 articles)

Rsultat :
"La commande fait moins de 10 articles" si le panier contient moins de 10 articles, sinon on naffiche rien


#FILTRE_infegal(#PANIER_NBART||10||La commande fait moins de 10 articles)

Rsultat :
"La commande fait moins de 10 articles" si le panier contient moins de 10 articles ou exactement 10 articles, sinon on naffiche rien

#FILTRE_egall{#PANIER_NBART||10||La commande contient 10 articles||la commande ne contient pas 10 articles}

Rsultat : 
"La commande contient 10 articles" si le panier contient exactement 10 articles. Sinon affichera "La commande ne contient pas 10 articles".

#FILTRE_inf{#PANIER_NBART||10||la commande ne contient pas 10 articles||la commande contient 10 articles}

Rsultat
"La commande ne contient pas 10 articles" si le nombre d'article dans le panier est diffrent de 10. Sinon affichera "La commande contient 10 articles"



Auteur : Mathieu Rastoix
	 rastoix.m@hotmail.fr

