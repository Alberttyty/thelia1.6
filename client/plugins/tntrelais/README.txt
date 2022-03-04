Ce plugin vous permet aussi de détecter les points relais TNT proche du client et de lui proposer.

Veuillez simplement glisser le répertoire tntrelais dans le dossier client/plugins/ de votre Thelia.
Vous pouvez aussi utiliser l'assistant d'import de plugins dans Configuration/Gestion des plugins.

boucle TNTRELAIS : 

paramètres en entrée : 
cpostal : code postal du client
ville : ville du client
idcommande : id d'une commande

paramètres en sorties : 
#NOM : nom du point relais
#CPOSTAL : code postal du point relais
#VILLE : ville du point relais
#ADRESSE : adresse du point relais
#CODE : code du point relais

Utilisation du plugin : 

<THELIA_adr type="ADRESSE" adresse="#ADRESSE_ACTIVE">

	 <T_tnt>
	 
		<THELIA_tnt type="TNTRELAIS" cpostal="#CPOSTAL" ville="#VILLE">
			
			<form method="POST" action="index.php?fond=commande&action=paiment&id=#ID">
			
			#NOM <br />
			#ADRESSE <br />
			#CPOSTAL #VILLE <br />
			<input type="hidden" name="nom" value="#NOM">
			<input type="hidden" name="adresse" value="#ADRESSE">
			<input type="hidden" name="cpostal" value="#CPOSTAL">
			<input type="hidden" name="ville" value="#VILLE">
			<input type="hidden" name="id" value="#ID">
			<input type="hidden" name="code" value="#CODE">
			<input type="submit" value="choisir" name="#ID">
			</form>
		</THELIA_tnt>

	</T_tnt>
		Pas de résultat
	 <//T_tnt>

</THELIA_adr>

vous pouvez aussi faire apparaitre le point relais dans les commandes du client lorsqu'il consulte son compte en rajoutant cette boucle à l'endroit qu'il vous convient dans commande_detail.html : 
<THELIA_veriftnt type="TNTRELAIS" idcommande="#ID">
		point relais : #NOM #ADRESSE #CPOSTAL #VILLE 
</THELIA_veriftnt>	


Auteur : Manuel Raynaud
