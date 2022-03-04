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

	function charger_listcont(dossier, type,objet){
		$('#select_prodcont').load(
				'ajax/contenu_associe.php',
				'action=contenu_assoc&type=' + type + '&objet='+objet+'&id_dossier=' + dossier
		);
	}

	function contenu_ajouter(id, type,objet){
		if(id)
			$('#contenuassoc_liste').load(
					'ajax/contenu_associe.php',
					'action=ajouter&type=' + type + '&objet='+objet+'&id='+ id,
					function() {
						charger_listcont($('#contenuassoc_dossier').val(), type,objet);
					}
			);
	}

	function contenuassoc_supprimer(id, type,objet){
		$('#contenuassoc_liste').load(
				'ajax/contenu_associe.php',
				'action=supprimer&type=' + type + '&objet='+objet+'&id='+ id,
				function(html){
					charger_listcont($('#contenuassoc_dossier').val(), type,objet);
				}
		);
	}

</script>