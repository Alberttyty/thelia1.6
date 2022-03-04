<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
?>
<?php

  require_once(__DIR__ . '/../../../fonctions/authplugins.php');
  autorisation("contenuaccueil");
  
  preg_match("`([^\/]*).php`", $_SERVER['PHP_SELF'], $page);
  
  if($page[1] == "contenu_modifier")
  
  {
?>

<link href="../client/plugins/contenuaccueil/css/contenuaccueil.css" rel="stylesheet" type="text/css" />
<link href="../client/plugins/contenuaccueil/css/datePicker.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript" src="../client/plugins/contenuaccueil/javascript/date.js"></script>
<script language="javascript" type="text/javascript" src="../client/plugins/contenuaccueil/javascript/date_fr.js"></script>
<script language="javascript" type="text/javascript" src="../client/plugins/contenuaccueil/javascript/jquery.datePicker.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	 initDateInput();
   
   $('#contenuaccueil input[name="visible"]').change(function (){
     initMasque();
   });
   
   initMasque();
   
});
function initMasque(){
   if($('#contenuaccueil input[name="visible"]').is(':checked')) $('#contenuaccueil span.masque ').hide();
   else {
    $('#contenuaccueil span.masque ').show();
    $('#contenuaccueil .dates input').val('');
  }
}
function initDateInput(){
   $('.date')
		.datePicker({createButton:false})
		.bind(
			'click',
			function()
			{
				$(this).dpDisplay();
				this.blur();
				return false;
			}
		);
    $('.datedebut').bind(
  		'dpClosed',
  		function(e, selectedDates)
  		{
  			var d = selectedDates[0];
  			if (d) {
  				d = new Date(d);
  				$(this).closest('td').find('.datefin').dpSetStartDate(d.addDays(1).asString());
  			}
  		}
  	);
  	$('.datefin').bind(
  		'dpClosed',
  		function(e, selectedDates)
  		{
  			var d = selectedDates[0];
  			if (d) {
  				d = new Date(d);
  				$(this).closest('td').find('.datedebut').dpSetEndDate(d.addDays(-1).asString());
  			}
  		}
  	);
}
</script>

<?php
  }
?>
