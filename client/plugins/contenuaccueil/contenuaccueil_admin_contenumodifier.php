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
  autorisation("voyages");
  
  include_once(realpath(dirname(__FILE__)) . "/Contenuaccueil.class.php");
  
  $contenu = new Contenu(lireParam("id", "int"));

  $contenuaccueil = new Contenuaccueil();

  $contenuaccueil->charger_contenu($contenu->id);

?>

<div id="contenuaccueil" class="origine">

  <div class="entete">
    <div class="titre">CONTENU EN PAGE D'ACCUEIL</div>
    <div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit();">VALIDER LES MODIFICATIONS</a></div>
  </div>
  
  <table><tbody>
  
    <!-- Visible -->
    <tr class="claire">
			<td class="designation">
        Contenu en page d'accueil
      </td>
			<td>
        <input type="checkbox" name="visible" value="oui" <?php if($contenuaccueil->visible=="oui") echo ' checked="checked" '; ?> />
      </td>
    </tr>
    <!-- Fin Visible -->
    
    <tr class="claire dates">
			<td class="designation">
        Dates de publication<br/>
        <span class="note">(facultatives)</span>
      </td>
			<td>
        <span class="masque_canvas">
        <span class="masque"></span>
        du <input type="text" class="form_court date datedebut" name="datedebut" value="<?php echo $contenuaccueil->convertirDate($contenuaccueil->datedebut); ?>" /> au  
        <input type="text" class="form_court date datefin" name="datefin" value="<?php echo $contenuaccueil->convertirDate($contenuaccueil->datefin); ?>" /> 
        </span>
        </td>
    </tr>
    
	</tbody></table>



</div>