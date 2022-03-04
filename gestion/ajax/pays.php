<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) OpenStudio		                                             */
/*		email : thelia@openstudio.fr		        	                          	 */
/*      web : http://www.openstudio.fr						   						 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program.  If not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/

require_once(__DIR__ . "/../pre.php");
require_once(__DIR__ . "/../auth.php");

require_once(__DIR__ . "/../../fonctions/divers.php");

if(! est_autorise("configuration")) exit;

header('Content-Type: text/html; charset=utf-8');

$action = lireParam('action', 'string');

if ($action == 'tva')
{
	$pays = new Pays();

	$id = intval(lireParam('pays', 'int'));

	if ($pays->charger($id))
	{
		$pays->tva = intval($_REQUEST['tva']);

		$pays->maj();
	}

	exit();
}
else if ($action == 'defaut')
{
	$pays = new Pays();

	$id = intval(lireParam('pays', 'int'));

	$pays->query("update $pays->table set `defaut`=0");
	$pays->query("update $pays->table set `defaut`=1 where id=$id");

	exit();
}
else if ($action == 'boutique') {
    $pays = new Pays();

    $id = intval(lireParam('pays', 'int'));

    $pays->query("update $pays->table set `boutique`=0");
    $pays->query("update $pays->table set `boutique`=1 where id=$id");

    exit();
}

/* Afficher la forme de modif/creation de pays */

$langues = array();

$langue = new Lang();

$result = $langue->query("select * from " . $langue->table . " order by id");

while ($result && $row = $langue->fetch_object($result))
{
	$langues[] = $row;
}

$pays = new Pays();
$paysdesc = new Paysdesc();

$id = intval(lireParam('pays', 'int'));

$pays->id = $id;

if ($id > 0)
{
	$pays->charger($id);
}

?>
<div class="entete_config" style="width: auto; float: none;">
			<div class="titre"><?php echo trad('EDITION', 'admin') ?></div>
			<div class="fonction_valider"><a href="#" onclick="$('#pays_edit_form').submit(); return false;"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin') ?></a></div>
		</div>

<div class="flottant">
<form method="post" action="pays.php" id="pays_edit_form">

	<input type="hidden" name="pays" value="<?php echo $pays->id; ?>" />
	<input type="hidden" name="offset" value="<?php echo $_REQUEST['offset']; ?>" />
	<input type="hidden" name="commande" value="edition" />

   <table cellpadding="5" cellspacing="0" style="border: 1px solid #9DACB6;">

     <tr class="fonce">
      <td class="designation" style="float: none;"><?php echo trad('Numéro ISO-3166', 'admin') ?></td>
      <td><input type="text" name="isocode" value="<?php echo htmlspecialchars($pays->isocode); ?>" /></td>
    </tr>

     <tr class="claire">
      <td class="designation" style="float: none;"><?php echo trad('Code alpha-2', 'admin') ?></td>
      <td><input type="text" name="isoalpha2" value="<?php echo htmlspecialchars($pays->isoalpha2); ?>" /></td>
    </tr>

     <tr class="fonce">
      <td class="designation" style="float: none;"><?php echo trad('Code alpha-3', 'admin') ?></td>
      <td><input type="text" name="isoalpha3" value="<?php echo htmlspecialchars($pays->isoalpha3); ?>" /></td>
    </tr>

     <tr class="claire">
      <td class="designation" style="float: none;"><?php echo trad('Soumis à la TVA française', 'admin') ?></td>
      <td>
        <input type="radio" name="tva" value="1" <?php echo $pays->tva == 1 ? 'checked="checked"' : '' ?>/><?php echo trad('Oui', 'admin'); ?>
        <input type="radio" name="tva" value="0" <?php echo $pays->tva == 0 ? 'checked="checked"' : '' ?>/> <?php echo trad('Non', 'admin'); ?>
      </td>
    </tr>

     <tr class="fonce">
      <td class="designation" style="float: none;"><?php echo trad('Zone de transport', 'admin'); ?></td>
      <td>
      <select name="zone">
      	<option value="0"><?php echo trad('Aucune', 'admin'); ?></option>
      	<?php
      	$zone = new Zone();
      	$result = $zone->query("select * from $zone->table order by nom");

      	while ($result && $zone = $zone->fetch_object($result, 'Zone'))
      	{
      		echo '<option value="'.$zone->id.'" '.($zone->id == $pays->zone ? 'selected="selected"' : '').'>'.$zone->nom.'</option>';
      	}
      	?>
      </select>
      </td>
    </tr>

      <?php foreach($langues as $langue)
      {
      	 if ($id > 0) $paysdesc->charger($id, $langue->id);
      ?>
	     <tr class="fonce">
	      <th colspan="2" style="height: 40px;">
	      	<img src="gfx/lang<?php echo $langue->id; ?>.gif" alt="<?php echo $langue->description ?>" style="vertical-align: bottom"/>
	      	<?php echo $langue->description ?>
	      	<input type="hidden" name="langue[<?php echo $langue->id; ?>]" value="<?php echo $langue->id; ?>" />
	      </th>
	    </tr>

	     <tr class="claire">
	      <td class="designation" style="float: none;"><?php echo trad('Nom', 'admin'); ?></td>
	      <td><input type="text" name="titre[<?php echo $langue->id; ?>]" value="<?php echo htmlspecialchars($paysdesc->titre); ?>" /></td>
	    </tr>

	     <tr class="fonce">
	      <td class="designation" style="float: none;"><?php echo trad('Chapo', 'admin'); ?></td>
	      <td><input type="text" name="chapo[<?php echo $langue->id; ?>]" value="<?php echo htmlspecialchars($paysdesc->chapo); ?>" /></td>
	    </tr>

	     <tr class="claire">
	      <td class="designation" style="float: none;"><?php echo trad('Description', 'admin'); ?></td>
	      <td><textarea style="height: 50px; width: 229px;" name="description[<?php echo $langue->id; ?>]"><?php echo $paysdesc->description; ?></textarea></td>
	    </tr>
	 <?php } ?>
  </table>
</form>
</div>