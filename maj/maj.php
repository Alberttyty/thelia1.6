<?php
	header("Content-type: text/html; charset=utf-8");

	$titre_page = "MISE A JOUR";
        include_once __DIR__ . '/../fonctions/error_reporting.php';
	include(__DIR__."/entete.php");
        define('IN_UPDATE_THELIA', true);
?>
<div id="chapeau"style="overflow:hidden;zoom: 1">

	<h2>installation de Thelia</h2>

	<br />

	Mise à jour en cours ...<br /><br />

	<?php
  require_once("../fonctions/mutualisation.php");
	require_once("config.php");
	require_once("../classes/Cnx.class.php");
  

	// Nécessaire pour éviter une boucle d'inclusion liée au 'once' des require.
        require_once("../classes/actions/ActionsModules.class.php");
    
        function query_patch($query) {
                $hdl = mysql_query($query);

                if (! $hdl) {
                        throw new Exception('<span class="erreur">Echec en accès à la base de données.</span><br />Détails: '.mysql_error().'<br />Requête: '.$query, 2);
                }
                else {
                    return $hdl;
                }
        }
        
        $erreur = 0;
        
        //vérification de la présence de certains dossiers/fichiers nécessaires à la mise à jour
        $basedir = __DIR__ . '/..';
        $neededFiles = array(
            $basedir . '/client.orig',
            $basedir . '/classes/Cnx.class.php.orig'
        );
        
        @clearstatcache();
        foreach($neededFiles as $file){
            if(file_exists($file) === false){
                $erreur = 1;
                echo '<span class="erreur">Le fichier ou répertoire '.str_replace($basedir, '', $file). ' n\'existe pas. Vous devez le copier depuis l\'archive de la version de Thelia que vous souhaitez installer</span><br />';
            }
        }
        
        unset($basedir);
        unset($neededFiles);
        unset($file);
        @clearstatcache();
        // fin de vérification des dossiers/fichiers nécessaires à la mise à jour
	$cnx = new Cnx();

	$queryver = "select * from variable where nom='version'";
	$resulver = mysql_query($queryver);

	if ($resulver && $row = mysql_fetch_object($resulver))
		$vcur = $row->valeur;
	else
		$vcur = "135";

	$vnew = $version;

	

	while($vcur != $vnew && $erreur == 0) {

            $vcur ++;

            $patch = "";
            
            $patch = rtrim(preg_replace("/(.)/", "$1.", $vcur), ".");

            $message = "";

            if (file_exists("patch/" . $patch . ".php")) {

                ?>
                Mise à jour vers <?php echo $patch; ?> ..............................
                <?php

                try {
                    require("patch/" . $patch . ".php");

                    ?>
                    OK<br />
                    <?php

                    // Un éventuel message mis à jour dans le patch.
                    if (! empty($message)) echo "<br />$message";
                }
                catch (Exception $ex) {
                    // $erreur = 1: erreur non fatale, on peut réessayer après avoir corrigé le problème
                    // $erreur = 2: erreur fatale, on ne peut pas MAJ.
                    // Le message de l'exception rapporte l'erreur.
                    $erreur = $ex->getCode();

                    ?>
                    <span class="erreur">ECHEC</span><br /><br />
                    <?php

                    echo $ex->getMessage();

                    break;
                } 
            }
	}
	?>

	<br />
	<?php if ($erreur == 0) { /* Aucune erreur */ ?>
		Mise à jour terminée.
		<br /><br />
		<span class="erreur">Pensez à supprimer le répertoire <strong>install</strong> de votre serveur !</span>

		<form action="../index.php" method="post">
			<input type="submit" value="Continuer" />
		</form>
	<?php } else if ($erreur == 1) { /* Erreur non-fatale: permettre de recommencer */ ?>
		<form action="maj.php">
			<br />
			Merci de corriger les points ci-dessus pour poursuivre la mise à jour.

			<input type="submit" value="Continuer" />
		</form>
	<?php } else { /* Erreur fatale: pas de MAJ possible */ ?>
		<br />
		Votre installation de Thelia ne peut être mis à jour.
	<?php } ?>
</div>

<?php
	include(__DIR__."/pied.php");
?>