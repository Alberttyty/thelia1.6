LE PLUGIN
------------------------------------------
Ce plugin vous permet d'afficher les meilleurs ventes de vos produit.
Il est inspiré du plugin mventes de Yoan De Macedo sans création de tables supplémentaires.

Contact et support: Laurent (elcanux@gmail.com).


UTILISATION
------------------------------------------
Dans la page que vous souhaitez voir les meilleurs ventes, utilisez la boucle proposée par le plugin. 

Exemple:

<h3>::Meilleurs Ventes::</h3>
<THELIA_meilleursventes type="DC_MEILLEURSVENTES" num="10">
	<T_prod>
			<THELIA_prod type="PRODUIT" ref="#REF"><a href="#REWRITEURL">#TITRE</a></THELIA_prod>
	</T_prod>
	<//T_prod>
</THELIA_meilleursventes>


LA BOUCLE
------------------------------------------
<THELIA_<nomboucle> type="dc_meilleursventes">

Paramètres:
  
   NUM:			Nombre de produit à afficher
   CLASSEMENT:	'vide': meilleurs ventes, 'inverse': mauvaises ventes
   EXCLUSION:	Référence, séparée par des virgules (ex: exclusion='ref01,ref2')
   
   
Variables:

   #PRODUIT:	L'id du produit
   #REF: 		la référence du produit
   #TITRE: 		le titre du produit
   #COMPTEUR:	le nombre de produit vendu

CHANGELOG
------------------------------------------
Version 1.3.1	Ajout de la variable 'COMPTEUR'
Version 1.3		Optimisation Thelia 1.5.1
Version 1.2		Ajout de #PRODUIT dans la boucle
Version 1.1		Correction proposé par roadster31
					- compter uniquement les ventes effectuées
					- erreur pour le bug paramètre exclusion
Version 1.0		Version initiale