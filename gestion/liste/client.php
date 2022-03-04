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

function liste_clients($order, $critere, $debut) {

	$i=0;

  	$client = new Client();

 	$query = "select * from $client->table order by $critere $order limit $debut,20";
  	$resul = $client->query($query);

  	while($resul && $row = $client->fetch_object($resul)){

  		$fond="ligne_".($i++%2 ? "claire":"fonce")."_rub";

		$commande = new Commande();
                $devise = new Devise();

		$querycom = "select id from $commande->table where client=$row->id and statut not in(".Commande::NONPAYE.",".Commande::ANNULE.") order by date DESC limit 0,1";
		$resulcom = $commande->query($querycom);
		$existe = 0;

		if($commande->num_rows($resulcom)>0){
			$existe = 1;
			$idcom = $commande->get_result($resulcom,0,"id");
			$commande->charger($idcom);


                        $devise->charger($commande->devise);

			$date = strftime("%d/%m/%y %H:%M:%S", strtotime($commande->date));
		}

		$creation = strftime("%d/%m/%y %H:%M:%S", strtotime($row->datecrea));
?>
<ul class="<?php echo($fond); ?>">
	<li style="width:122px;"><?php echo($row->ref); ?></li>
	<li style="width:110px;"><?php echo($creation); ?></li>
	<li style="width:143px;"><?php echo($row->entreprise); ?></li>
	<li style="width:243px;"><?php echo($row->nom); ?> <?php echo($row->prenom); ?></li>
	<li style="width:110px;"><?php if($existe) echo $date; ?></li>
	<li style="width:63px;"><?php if($existe){ echo formatter_somme($commande->total(true, true)).' '.$devise->symbole; }?></li>
	<li style="width:40px;"><a href="client_visualiser.php?ref=<?php echo($row->ref); ?>" class="txt_vert_11"><?php echo trad('editer', 'admin'); ?></a></li>
	<li style="width:25px; text-align:center;"><a href="#" onclick="confirmSupp('<?php echo($row->ref); ?>')"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>
<?php }
}
?>