/*-----------------------------------------------------------------------------------*/
/*      Copyright (c) BELKACEM Karim                                                 */
/*		email : karim@ergonomind.com                                                 */
/*      web : http://www.ergonomind.com                                              */
/*-----------------------------------------------------------------------------------*/
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

Pour l’utilisation, c’est simple il suffit d’installer le plugin comme habituellement dans Thelia, ensuite dans votre template vous faites : #FILTRE_datefrancaise(#DATE) ou #FILTRE_datefrancaise(#DATE #HEURE). Pour cette dernière il faut décommenter le version avec l’heure et commenter celle sans, vous verrez c’est indiqué dans le fichier "Filtredatefrancaise.class.php" du zip !

Exemple d’utilisation dans une boucle contenu :

Ceci donnera : jeudi 13 août 2009

<THELIA_LESACTUS type="DOSSIER" id="2">
<h3>#TITRE</h3>
<ul><THELIA_ACTU type="CONTENU" dossier="#ID" num="5" classement="inverse"><li>
<small>Posté le #FILTRE_datefrancaise(#DATE)</small>
<h4>#TITRE</h4>
#DESCRIPTION
</li></THELIA_ACTU>
</ul>
</THELIA_LESACTUS>

Ceci donnera : jeudi 13 août 2009 à 11h 30min 55s

<THELIA_LESACTUS type="DOSSIER" id="2">
<h3>#TITRE</h3>
<ul><THELIA_ACTU type="CONTENU" dossier="#ID" num="5" classement="inverse"><li>
<small>Posté le #FILTRE_datefrancaise(#DATE #HEURE)</small>
<h4>#TITRE</h4>
#DESCRIPTION
</li></THELIA_ACTU>
</ul>
</THELIA_LESACTUS>