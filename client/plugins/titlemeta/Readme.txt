Plugin permettant de personnaliser sur chaque page du site le titre, la balise meta description et la balise meta keywords

installation :

1 - mettre le contenu du zip dans le répertoire client/plugins/
2 - activer le plugins dans le back-office (configuration->activation des plugins -> titlemeta)
3 - ajouter dans le html (ou dans les html meta.html, meta_contenu.html, meta_dossier.html, meta_produit.html, meta_rubrique.html) entre les balises <head></head> la boucle suivante :

Exemple de boucle pour une page contenu :

<THELIA_TITLEMETA type="TITLEMETA" page="CONTENU" id="#CONTENU_ID">

    <title>#TITLE</title>

    <meta name="description" content="#META" />
    <meta name="keywords" lang="#LNGCODE" content="#KEYWORD" />

</THELIA_TITLEMETA>

Le mode par défault prend le titre et meta de la page en cours : produit, rubrique, contenu, ou dossier.
Si rien est trouvé le plugin cherche dans les parents et si rien est trouvé il met ce qui a été rentré dans la gestion des plugins (modules).

Si vous voulez avoir uniquement les informations de la page en cours sans remonter dans l'architecture du site mettre mode="solo".

Plugin modifié par Virgil Calabrese le 28/05/2013 - vcalabrese@openstudio.fr