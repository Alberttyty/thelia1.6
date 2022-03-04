<div id="contenu_int">
    <p align="left">
        <a href="accueil.php" class="lien04">Accueil</a>
        <img src="gfx/suivant.gif" width="12" height="9" border="0" />
        Import / Export
    </p>

    <?php require_once(realpath(dirname(__FILE__)) . "/kintpv_formulaire.php");

    function maxClassement($idCarac, $lang) {
    		$Caracdispdesc = new Caracdispdesc();
    		$Caracdisp = new Caracdisp();

    		$query = 'SELECT max(ddd.classement) as maxClassement
    			        FROM '.$Caracdispdesc->table.' ddd
    			        LEFT JOIN '.$Caracdisp->table.' dd on dd.id = ddd.caracdisp
    			        WHERE lang='.$lang.'
    			        AND dd.caracteristique='.$idCarac;

    		$resul = $Caracdispdesc->query($query);

       	return $resul ? intval($Caracdispdesc->get_result($resul, 0, "maxClassement")) : 0;
  	}
    ?>
</div>
