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

Ce plugin d'envoyer un mail au client venant de créer un compte.

Veuillez simplement glisser le répertoire mailnouvcli dans le dossier client/plugins de votre Thelia.

Vous pouvez modifier le message "Mail envoyé lors de l'inscription d'un client"
depuis votre Back-Office, sachat que les variables suivantes sont disponibles
dans le titre et les dale le titre, le mes :

	__CLIENT_RAISON__ : Civilité (Monsieur, Madame, Mademoiselle)
	__CLIENT_ENTREPRISE__ : Nom de l'entreprise du client
	__CLIENT_SIRET__ : Numéro SIRET
	__CLIENT_INTRACOM__ : Numéro de TVA intra-communautaire
	__CLIENT_NOM__ : Nom du client
	__CLIENT_PRENOM__ : Prénom du client
	__CLIENT_ADRESSE1__ : Ligne d'adresse 1
	__CLIENT_ADRESSE2__ : Ligne d'adresse 2
	__CLIENT_ADRESSE3__ : Ligne d'adresse 3
	__CLIENT_CPOSTAL__ : Code postal
	__CLIENT_VILLE__ : Ville
	__CLIENT_PAYS__ : Nom du pays
	__CLIENT_TELFIXE__ : No. de téléphone fixe
	__CLIENT_TELPORT__ : No. de téléphone mobile
	__CLIENT_EMAIL__ : Adresse email du client
	__CLIENT_TYPE__ : Type (?) du client
	__CLIENT_POURCENTAGE__ : pourcentage de remise
	__CLIENT_MOTDEPASSE__ : Mot de passe entré.
	__URLSITE__ : URL du site
	__NOMSITE__ : Nom du site

Auteur du plugin : Yoan De Macedo
	 			   yoandm@gmail.com