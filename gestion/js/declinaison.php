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
?>
<script type="text/javascript">

	function declinaison_ajouter(declinaison){
		$("#declinaison_liste").load(
			"ajax/declinaison.php",
			"rubrique=<?php echo $_REQUEST['id']; ?>&declinaison="+declinaison+"&action=ajouter",
			function(){
				charger_liste_decli(<?php echo $_REQUEST["id"]; ?>);
			}
		);
	}

	function charger_liste_decli(rubrique){
		$("#liste_prod_decli").load(
			"ajax/declinaison.php",
			"action=liste&id="+rubrique
		);
	}

	function declinaison_supprimer(declinaison){
		$("#declinaison_liste").load(
			"ajax/declinaison.php",
			"action=supprimer&declinaison="+declinaison+"&rubrique=<?php echo $_REQUEST["id"]; ?>",
			function(){
				charger_liste_decli(<?php echo $_REQUEST["id"]; ?>);
			}
		);
	}

</script>