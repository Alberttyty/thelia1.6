<?php
/*****************************************************************************
 *
 * Auteur   : Bruno | atnos.com (contact: contact@atnos.com)
 * Version  : 0.1
 * Date     : 29/07/2007
 *
 * Copyright (C) 2007 Bruno PERLES
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *****************************************************************************/
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/traduction.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Navigation.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Pays.class.php");
require_once(SITE_DIR."/client/plugins/paypal/config_okdhsu74plk5.php");

if (ActionsLang::instance()->id_langue_courante_defini()) $lang = ActionsLang::instance()->get_id_langue_courante();
else $lang = ActionsLang::instance()->get_id_langue_defaut();

require_once(realpath(dirname(__FILE__)) . "/lang/".$lang.".php");

session_start();

$total = 0;
$total = $_SESSION['navig']->commande->total;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Paypal</title>
<style type="text/css">
body { margin: 0; padding: 0; background-color: #eee; }
#global {
	position:absolute;
	left: 50%; top: 50%;
	width: 400px; max-width:100%; height: 170px;
	margin-left: -200px; margin-top: -85px; /* Moitié largeur et Moitié hauteur */
	text-align: center;
  background-color: transparent;
}
#global span { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; }
</style>
</head>
<body onload="document.getElementById('formpaypal').submit()">
<?php
// Référence
$Reference_Cde = urlencode($_SESSION['navig']->commande->ref);

// Montant
$Montant = $total;

// Pays
$pays= new Pays();
$pays->charger($_SESSION["navig"]->client->pays);
?>
    <div id="global">
        <span><?php echo trad('redirection',"paypal"); ?></span>
        <br/><br/>
        <form action="<?php echo $serveur; ?>" id="formpaypal" method="post">
      		  <div>
            		<input type="hidden" name="charset" value="utf-8" />
            		<input type="hidden" name="upload" value="1" />
            		<input type="hidden" name="first_name" value="<?php echo str_replace("\"","",$_SESSION["navig"]->client->prenom); ?>" />
            		<input type="hidden" name="last_name" value="<?php echo str_replace("\"","",$_SESSION["navig"]->client->nom); ?>" />
            		<input type="hidden" name="address1" value="<?php echo str_replace("\"","",$_SESSION["navig"]->client->adresse1); ?>" />
            		<?php if($_SESSION["navig"]->client->adresse2 != "")
            		echo('<input type="hidden" name="address2" value="'.str_replace("\"","",$_SESSION["navig"]->client->adresse2).'" />');
            		?>
            		<input type="hidden" name="city" value="<?php echo str_replace("\"","",$_SESSION["navig"]->client->ville); ?>" />
            		<input type="hidden" name="zip" value="<?php echo str_replace("\"","",$_SESSION["navig"]->client->cpostal); ?>" />
                <input type="hidden" name="country" value="<?php echo str_replace("\"","",$pays->isoalpha2); ?>" />
            		<input type="hidden" name="amount" value="<?php echo round($Montant, 2); ?>" />
            		<input type="hidden" name="email" value="<?php echo $_SESSION["navig"]->client->email; ?>">
            		<input type="hidden" name="shipping_1" value="<?php echo $_SESSION["navig"]->commande->port; ?>" />
                <input type="hidden" name="discount_amount_cart" value="<?php echo $_SESSION["navig"]->commande->remise; ?>" />

            		<?php
            		$venteprod = new Venteprod();
            		$query = "SELECT * FROM $venteprod->table WHERE commande=".$_SESSION["navig"]->commande->id;
            		$resul = mysql_query($query);
            		$i=0;
            		while($row = mysql_fetch_object($resul)) {
            			$i++;
            			?>
            			<input type="hidden" name="item_name_<?php echo($i); ?>" value="<?php echo str_replace("\"","",trim($row->titre)); ?>" />
            			<input type="hidden" name="amount_<?php echo($i); ?>" value="<?php echo $row->prixu; ?>" />
            			<input type="hidden" name="quantity_<?php echo($i); ?>" value="<?php echo $row->quantite; ?>" />
            		<?php
            			}
            		?>

            		<input type="hidden" name="business" value="<?php echo $compte_paypal; ?>" />
            		<input type="hidden" name="receiver_email" value="<?php echo $compte_paypal; ?>" />
            		<input type="hidden" name="cmd" value="_cart" />
            		<input type="hidden" name="currency_code" value="<?php echo $devise; ?>" />
            		<input type="hidden" name="payer_id" value="<?php echo $_SESSION["navig"]->client->id; ?>" />
            		<input type="hidden" name="payer_email" value="<?php echo $_SESSION["navig"]->client->email; ?>" />
            		<input type="hidden" name="return" value="<?php echo $retourok; ?>" />
            		<input type="hidden" name="notify_url" value="<?php echo $confirm; ?>" />
            		<input type="hidden" name="cancel_return" value="<?php echo $retournok; ?>" />
            		<input type="hidden" name="invoice" value="<?php echo $Reference_Cde; ?>" />

            		<input type="image" src="images/pp_cc_mark_74x46.jpg" alt="Paypal"/>
      	   </div>
    	 </form>
    </div>
</body>
</html>
