* Description  *************************************************************

Ce plugin permet de connaitre le produit précédent et le produit suivant.


* Utilisation  *************************************************************

Paramètres d'entrée
ref        : la référence du produit
precedent  : 0 ou 1 (produit précédent)
suivant	   : 0 ou 1 (produit suivant)
rubrique   : id de la rubrique 
classement : date, prixmax, prixmin, rubrique, manuel, inverse, titre, titreinverse

Paramètres de sortie
#REFPREC   : Référence du produit précédent
#REFSUIV   : Référence du produit suivant 


* Exemple      *************************************************************

Produit précédent :
<THELIA_prec type="DC_PRODUITSUIVPREC" ref="#REF" precedent="1" rubrique="#RUBRIQUE" classement="titre">
	<a href="index.php?fond=produit&ref=#REFPREC"><< PRECEDENT</a>
</THELIA_prec>

Produit suivant :
<THELIA_prec type="DC_PRODUITSUIVPREC" ref="#REF" suivant="1" rubrique="#RUBRIQUE" classement="titre">
	<a href="index.php?fond=produit&ref=#REFSUIV">SUIVANT >></a>
</THELIA_prec>
