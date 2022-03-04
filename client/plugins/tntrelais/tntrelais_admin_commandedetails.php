<?php
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation(Tntrelais::MODULE);
require_once(dirname(__FILE__) . "/Tntrelais.class.php");

$temptnt = new Tntrelais();
$tmp_cmd = new Commande();
$tmp_cmd->charger_ref($_GET['ref']);

if($temptnt->existe($tmp_cmd->id)){
?>

<table width="710" border="0" cellpadding="5" cellespacing="0">
    <tr><td width="600" height="30" class="titre_cellule_tres_sombre">STATUT DU TRANSPORTEUR </td></tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td height="30" align="left" valign="middle" class="titre_cellule">point relais tnt</td>
        <td class="cellule_claire">
        <?php
            $query = mysql_query("SELECT * FROM $temptnt->table WHERE id_commande =\"$tmp_cmd->id\" ");
            $row = mysql_fetch_object($query);
            echo $row->nom." ".$row->adresse." ".$row->cpostal." ".$row->ville;
		    ?>
        </td>
    </tr>
</table>
<?php } ?>
<br />
