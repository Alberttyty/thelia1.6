<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

function modifier_pays_zone($idzone) {

	$zone = new Zone();
	$zone->charger($idzone);

	$pays = new Pays();
	$query = "select * from $pays->table where zone=\"-1\"";
	$resul = $pays->query($query);
?>
	<div class="entete_liste_config" style="margin-top:15px;">
		<div class="titre"><?php echo trad('MODIFICATION_ZONE', 'admin'); ?></div>
	</div>

	<ul class="ligne1">
		<li style="width:250px;">
			<select class="form_select" id="pays">
			<?php
				while($resul && $row = $pays->fetch_object($resul)){
					$paysdesc = new Paysdesc();
					if ($paysdesc->charger($row->id)) {
          /*if (strpos($paysdesc->titre,'Canada') !== false) {*/   
			?>
	     	<option value="<?php echo $paysdesc->pays; ?>"><?php echo $paysdesc->titre; ?></option>
			<?php
				  /*}*/
          }
				}
			?>
			</select>
		</li>
		<li><a href="javascript:ajouter($('#pays').val())"><?php echo trad('AJOUTER_PAYS', 'admin'); ?></a></li>
	</ul>

<?php
		$pays = new Pays();
		$query = "select * from $pays->table where zone=\"" . $idzone . "\"";
		$resul = $pays->query($query);
?>
<?php
		while($resul && $row = $pays->fetch_object($resul)){
			$paysdesc = new Paysdesc();
			$paysdesc->charger($row->id);

			$fond="ligne_".($i++%2 ? "fonce":"claire")."_BlocDescription";
?>
		<ul class="<?php echo $fond; ?>">
			<li style="width:492px;"><?php echo $paysdesc->titre; ?></li>
			<li style="width:32px;"><a href="javascript:supprimer(<?php echo $row->id; ?>)"><?php echo trad('Supprimer', 'admin'); ?></a></li>
		</ul>
<?php
		}
?>
	<ul class="ligne1">
			<li><?php echo trad('Forfait transport: ', 'admin'); ?><input type="text" class="form_inputtext" id="forfait" onclick="this.value=''" value="<?php echo htmlspecialchars($zone->unite); ?>" /></li>
			<li><a href="javascript:forfait($('#forfait').val())"><?php echo trad('VALIDER', 'admin'); ?></a></li>
	</ul>
<?php } ?>