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

	function charger_listacc(rubrique){
		$('#select_prodacc').load(
			'ajax/accessoire.php',
			'action=produit&ref=<?php echo $_GET['ref']; ?>&id_rubrique=' + rubrique
		);
	}

	function accessoire_ajouter(id){
		if (id)
			$('#accessoire_liste').load(
				'ajax/accessoire.php',
				'action=ajouter&ref=<?php echo $_GET['ref']; ?>&id='+ id,
				function(){
					charger_listacc($('#accessoire_rubrique').val());
				}
			);
	}

	function accessoire_supprimer(id){
		$('#accessoire_liste').load(
			'ajax/accessoire.php',
			'action=supprimer&ref=<?php echo $_GET['ref']; ?>&id='+ id,
			function(){
				charger_listacc($('#accessoire_rubrique').val());
			}
		);
	}

</script>