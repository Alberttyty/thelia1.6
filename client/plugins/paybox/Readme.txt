Plugin Paybox permettant de s'interfacer avec le moyen de paiement Paybox (www.paybox.com)

INSTALLATION
============

1. Mise en place du plugin : Uploader le fichier paybox.zip via l'interface d'administration de Thelia (Configuration -> Activation des plugins)

2. Activer le plugin (lien "activer" sur la ligne du plugin.

3. Rendez-vous dans Modules -> Paybox vous pourrez ainsi renseigner les valeurs fournis par paybox. Pour le champs clé privé d'échange, suivre les indications qui suivent : 

4. génération de la clé privé d'échange : 

    a. L’interface de génération de la clé secrète d’authentification se trouve dans l’onglet « Informations » du Back Office Commerçant de Paybox, en bas de la page.
    b. Le champ « Phrase de passe » peut être renseigné avec une phrase, un mot de passe, ou tout autre texte.
    c. L’affichage par défaut du champ « Phrase de passe » est caché, les caractères apparaissent comme un champ « mot de passe ». Il est possible de choisir d’afficher cette phrase de passe en décochant la case « Cacher ».
    d. Les champs « Complexité » et « Force » sont mis à jour automatiquement lorsque la phrase de passe est saisie. Ces champs permettent de définir des règles d’acceptation minimales de la phrase de passe. Les règles fixées actuellement demandent une phrase de passe d’au moins 15 caractères de long et d’une force de 90%. Le bouton « VALIDER » restera grisé tant que ces limitations ne sont pas respectées.
    e. Le bouton « Générer une clé » permet de calculer la clé d’authentification à partir de la phrase de passe saisie. Ce calcul est une méthode standard assurant le caractère aléatoire de la clé et renforçant sa robustesse. Cette méthode de calcul étant fixe, il est possible à tout moment de retrouver sa clé en retapant la même phrase de passe et en relançant le calcul.
    f. Attention, il est possible que le calcul de la clé prenne quelques secondes, selon le navigateur Internet utilisé et la puissance de l’ordinateur. Au cours du calcul, il se peut que le navigateur Internet Explorer demande s’il faut « arrêter l’exécution de ce script ». Il faut répondre « Non » à cette alerte, et patienter jusqu’à la fin du calcul.
    g. Une fois le calcul terminé, la clé sera affichée dans le champ « Clé ». Il faut alors copier cette clé d’authentification et la copier dans le champs "clé privé d'échange" dans le back office de Thelia (Modules -> Paybox)
    h. Après validation du formulaire, un message récapitulatif sera affiché sur la page, expliquant qu’un email de demande de confirmation a été envoyé à l’adresse mail du Commerçant. La clé qui vient d’être générée ne sera pas active tant que les indications de validation décrites dans cet email n’auront pas été appliquées.
    i. Si besoin est, changer l'url du serveur de paybox dans le fichier config.php
