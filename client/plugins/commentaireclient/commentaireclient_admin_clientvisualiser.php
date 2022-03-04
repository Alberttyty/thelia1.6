<?php

include_once(dirname(__FILE__) . "/../../../fonctions/authplugins.php");

include_once(dirname(__FILE__) . "/Commentaireclient.class.php");

autorisation(Commentaireclient::MODULE);

$ref = $_REQUEST["ref"];

$commentaire = new Commentaireclient();

if (isset($_REQUEST["commclient_event"]) && $_REQUEST["commclient_event"] == "add") {

	$texte = $_REQUEST["commentaire"];

	if (!$commentaire->charger($ref)) {

		$commentaire->client = $ref;
		$commentaire->commentaire = $texte;

		$commentaire->add();
	}
	else {
		$commentaire->commentaire = $texte;

		$commentaire->maj();
	}
}

$commentaire->charger($ref);

?>
<div class="entete_liste_client">
	<div class="titre">COMMENTAIRE</div>
</div>
<form method="post" action="client_visualiser.php">
	<input type="hidden" name="ref" value="<?php echo $ref; ?>">
	<input type="hidden" name="commclient_event" value="add">

	<ul style="background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;" class="ligne_claire_BlocDescription">
		<li>
			<textarea name="commentaire" style="width: 567px; height: 100px;"><?php echo $commentaire->commentaire; ?></textarea>
		</li>

		<li class="designation" style="width: 100%">
			<input type="submit" value="Enregistrer" />
		</li>
	</ul>
</form>